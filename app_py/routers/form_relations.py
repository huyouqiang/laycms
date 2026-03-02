import hashlib
import re
from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, JSONResponse
from sqlalchemy import func
from sqlalchemy.orm import Session

from app_py.database import get_db, db_execute_raw, db_fetchone, db_fetchall, table_exists, get_table_columns, column_exists
from app_py.dependencies import require_permission
from app_py.models import CmsUser, Form as FormModel, FormField, FormRelation
from app_py.utils import url_for, json_serializable

router = APIRouter()

_FIELD_NAME_RE = re.compile(r"^[a-z][a-z0-9_]*$")


def _get_column_type(table: str, column: str) -> str:
    row = db_fetchone(
        "SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1",
        {"t": table, "c": column},
    )
    return row[0] if row else "bigint"


def _add_column_for_relation(table: str, related_table: str, column: str, related_column: str):
    if column_exists(table, column):
        return
    col_type = _get_column_type(related_table, related_column)
    db_execute_raw(f"ALTER TABLE `{table}` ADD COLUMN `{column}` {col_type} NULL")


def _ensure_column_type_for_fk(table: str, column: str, related_table: str, related_column: str):
    """确保本表列类型与关联表列一致，以便添加外键"""
    if not column_exists(table, column):
        return
    cur_type = _get_column_type(table, column)
    need_type = _get_column_type(related_table, related_column)
    if cur_type.lower().replace(" ", "") != need_type.lower().replace(" ", ""):
        db_execute_raw(f"ALTER TABLE `{table}` MODIFY COLUMN `{column}` {need_type} NULL")


def _add_foreign_key(table: str, column: str, related_table: str, related_column: str):
    """添加 MySQL 外键约束"""
    _ensure_column_type_for_fk(table, column, related_table, related_column)
    rows = db_fetchall(
        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE "
        "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c AND REFERENCED_TABLE_NAME = :rt",
        {"t": table, "c": column, "rt": related_table},
    )
    if rows:
        return
    fk_name = f"fk_{table}_{column}"
    if len(fk_name) > 64:
        fk_name = "fk_" + hashlib.md5(f"{table}_{column}".encode()).hexdigest()[:16]
    db_execute_raw(
        f"ALTER TABLE `{table}` ADD CONSTRAINT `{fk_name}` "
        f"FOREIGN KEY (`{column}`) REFERENCES `{related_table}` (`{related_column}`) ON DELETE SET NULL ON UPDATE CASCADE"
    )


def _drop_foreign_key(table: str, column: str):
    rows = db_fetchall(
        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE "
        "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c AND REFERENCED_TABLE_NAME IS NOT NULL",
        {"t": table, "c": column},
    )
    for r in rows:
        try:
            db_execute_raw(f"ALTER TABLE `{table}` DROP FOREIGN KEY `{r[0]}`")
        except Exception:
            pass


@router.post("/")
async def store(
    request: Request,
    form_id: int = Form(...),
    field_name: str = Form(""),
    form_field_id: int = Form(0),
    related_form_id: int = Form(...),
    related_field_name: str = Form(...),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    related_form = db.query(FormModel).filter(FormModel.id == related_form_id).first()
    if not related_form:
        raise HTTPException(404)
    if form.id == related_form.id:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "不能关联到自身"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    related_field_name = related_field_name.strip()[:64]
    if table_exists(related_form.table_name):
        cols = get_table_columns(related_form.table_name)
        if related_field_name not in cols:
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(400, detail={"code": 1, "msg": "关联表中不存在该字段"})
            return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    form_field = None
    if form_field_id:
        form_field = db.query(FormField).filter(FormField.form_id == form.id, FormField.id == form_field_id).first()
        if not form_field:
            raise HTTPException(404)
        if db.query(FormRelation).filter(FormRelation.form_field_id == form_field.id).first():
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(400, detail={"code": 1, "msg": "该字段已被用于关联"})
            return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
        form_field.form_control = "relation"
        db.commit()
    else:
        field_name = (field_name or "").lower().strip()
        if not field_name:
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(400, detail={"code": 1, "msg": "请选择现有字段或输入新字段名"})
            return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
        if not _FIELD_NAME_RE.match(field_name):
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(400, detail={"code": 1, "msg": "字段名只能包含小写字母、数字和下划线"})
            return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
        form_field = db.query(FormField).filter(FormField.form_id == form.id, FormField.field_name == field_name).first()
        if form_field:
            if db.query(FormRelation).filter(FormRelation.form_field_id == form_field.id).first():
                if "application/json" in request.headers.get("accept", ""):
                    raise HTTPException(400, detail={"code": 1, "msg": "该字段已被用于关联"})
                return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
            form_field.form_control = "relation"
            db.commit()
        else:
            max_order = db.query(func.max(FormField.sort_order)).filter(FormField.form_id == form.id).scalar() or 0
            form_field = FormField(
                form_id=form.id,
                field_name=field_name,
                label=f"{related_form.name}ID",
                form_control="relation",
                sort_order=int(max_order) + 1,
                is_required=False,
                is_list_visible=True,
            )
            db.add(form_field)
            db.commit()
            db.refresh(form_field)
            _add_column_for_relation(form.table_name, related_form.table_name, field_name, related_field_name)
    relation = FormRelation(
        form_id=form.id,
        form_field_id=form_field.id,
        related_form_id=related_form.id,
        related_field_name=related_field_name,
    )
    db.add(relation)
    db.commit()
    db.refresh(relation)
    try:
        _add_foreign_key(form.table_name, form_field.field_name, related_form.table_name, related_field_name)
    except Exception as e:
        db.delete(relation)
        db.commit()
        return JSONResponse({"code": 1, "msg": f"MySQL 外键添加失败: {e}"}, status_code=400)
    from sqlalchemy.orm import joinedload
    rel = db.query(FormRelation).options(
        joinedload(FormRelation.form_field),
        joinedload(FormRelation.related_form),
    ).filter(FormRelation.id == relation.id).first()
    data = {"id": rel.id, "form_id": rel.form_id, "form_field_id": rel.form_field_id, "related_form_id": rel.related_form_id, "related_field_name": rel.related_field_name}
    if rel.form_field:
        data["form_field"] = {"id": rel.form_field.id, "field_name": rel.form_field.field_name, "label": rel.form_field.label}
    if rel.related_form:
        data["related_form"] = {"id": rel.related_form.id, "name": rel.related_form.name, "table_name": rel.related_form.table_name}
    return JSONResponse(json_serializable({"code": 0, "msg": "关联添加成功", "data": data}))


@router.delete("/{relation_id}")
async def destroy(
    request: Request,
    relation_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    from sqlalchemy.orm import joinedload
    relation = db.query(FormRelation).options(
        joinedload(FormRelation.form),
        joinedload(FormRelation.form_field),
    ).filter(FormRelation.id == relation_id).first()
    if not relation:
        raise HTTPException(404)
    form = relation.form
    _drop_foreign_key(form.table_name, relation.form_field.field_name)
    db.delete(relation)
    db.commit()
    return JSONResponse({"code": 0, "msg": "关联已删除"})


@router.get("/{form_id}/related-columns")
async def get_related_columns(
    form_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "read")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    if not table_exists(form.table_name):
        return JSONResponse({"columns": []})
    columns = get_table_columns(form.table_name)
    return JSONResponse({"columns": columns})
