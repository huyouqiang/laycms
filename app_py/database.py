from sqlalchemy import create_engine, text
from sqlalchemy.orm import sessionmaker, Session
from sqlalchemy.pool import QueuePool

from app_py.config import DATABASE_URL

engine = create_engine(
    DATABASE_URL,
    poolclass=QueuePool,
    pool_pre_ping=True,
    echo=False,
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


def db_execute_raw(sql: str, params: dict = None):
    with engine.connect() as conn:
        conn.execute(text(sql), params or {})
        conn.commit()


def db_fetchall(sql: str, params: dict = None):
    with engine.connect() as conn:
        result = conn.execute(text(sql), params or {})
        return result.fetchall()


def db_fetchone(sql: str, params: dict = None):
    with engine.connect() as conn:
        result = conn.execute(text(sql), params or {})
        return result.fetchone()


def table_exists(table_name: str) -> bool:
    r = db_fetchone(
        "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1",
        {"t": table_name},
    )
    return r is not None


def column_exists(table_name: str, column_name: str) -> bool:
    r = db_fetchone(
        "SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1",
        {"t": table_name, "c": column_name},
    )
    return r is not None


def get_table_columns(table_name: str) -> list:
    rows = db_fetchall(
        "SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t ORDER BY ordinal_position",
        {"t": table_name},
    )
    return [r[0] for r in rows] if rows else []


def get_column_type(table_name: str, column_name: str) -> str:
    """获取列的完整类型定义（如 varchar(255), bigint, text）"""
    row = db_fetchone(
        "SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1",
        {"t": table_name, "c": column_name},
    )
    return row[0] if row else "varchar(255)"
