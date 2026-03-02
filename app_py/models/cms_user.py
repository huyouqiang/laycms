from sqlalchemy import Column, BigInteger, String, Boolean, ForeignKey
from sqlalchemy.orm import relationship
import bcrypt

from app_py.models.base import Base


class CmsUser(Base):
    __tablename__ = "cms_users"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    username = Column(String(64), nullable=False, unique=True)
    password = Column(String(255), nullable=False)
    nickname = Column(String(64), nullable=True)
    user_group_id = Column(BigInteger, ForeignKey("user_groups.id"), nullable=True)
    is_root = Column(Boolean, default=False)
    is_active = Column(Boolean, default=True)

    user_group = relationship("UserGroup", back_populates="users")

    def _truncate_password(self, raw: str) -> bytes:
        """Bcrypt 只接受最多 72 字节，超出需截断（bcrypt 5.0+ 会报错）"""
        encoded = raw.encode("utf-8")
        return encoded[:72] if len(encoded) > 72 else encoded

    def set_password(self, raw: str):
        pwd = self._truncate_password(raw)
        self.password = bcrypt.hashpw(pwd, bcrypt.gensalt()).decode("utf-8")

    def check_password(self, raw: str) -> bool:
        pwd = self._truncate_password(raw)
        if isinstance(self.password, str):
            stored = self.password.encode("utf-8")
        else:
            stored = self.password
        return bcrypt.checkpw(pwd, stored)

    def has_permission(self, table_name: str, action: str, db) -> bool:
        if self.is_root:
            return True
        if not self.user_group_id:
            return False
        from app_py.models.user_group import GroupPermission
        perm = db.query(GroupPermission).filter(
            GroupPermission.user_group_id == self.user_group_id,
            GroupPermission.table_name == table_name,
        ).first()
        if not perm:
            return False
        return getattr(perm, f"can_{action}", False)

    def can_access_table(self, table_name: str, action: str, db) -> bool:
        return self.has_permission(table_name, action, db)
