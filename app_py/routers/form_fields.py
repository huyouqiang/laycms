import re
from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy import func
from sqlalchemy.orm import Session, joinedload

from app_py.database import get_db, db_execute_raw, table_exists, get_column_type
from app_py.dependencies import require_permission
from app_py.models import CmsUser, Form as FormModel, FormField
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()

_FIELD_NAME_RE = re.compile(r"^[a-z][a-z0-9_]*$")
_CONTROL_TYPES = ("input", "textarea", "select", "radio", "checkbox", "date", "datetime", "number", "file", "editor", "relation")


def _add_column_to_table(table: str, field: FormField):
    type_map = {
        "number": "BIGINT",
        "relation": "BIGINT",
        "date": "DATE",
        "datetime": "DATETIME",
        "textarea": "TEXT",
        "editor": "TEXT",
        "file": "VARCHAR(500)",
        "radio": "VARCHAR(255)",
        "checkbox": "VARCHAR(255)",
        "select": "VARCHAR(255)",
    }
    col_type = type_map.get(field.form_control, "VARCHAR(255)")
    nullable = "NOT NULL" if field.is_required else "NULL"
    db_execute_raw(f"ALTER TABLE `{table}` ADD COLUMN `{field.field_name}` {col_type} {nullable}")


def _drop_column_from_table(table: str, column: str):
    db_execute_raw(f"ALTER TABLE `{table}` DROP COLUMN `{column}`")


def _modify_column_nullable(table: str, column: str, nullable: bool):
    """修改列的 NULL/NOT NULL 约束"""
    col_type = get_column_type(table, column)
    null_clause = "NOT NULL" if nullable else "NULL"
    db_execute_raw(f"ALTER TABLE `{table}` MODIFY COLUMN `{column}` {col_type} {null_clause}")


@router.get("/{form_id}", response_class=HTMLResponse)
async def index(
    request: Request,
    form_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "read")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).options(joinedload(FormModel.fields)).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    from app_py.dependencies import get_menu_form_groups
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "form-fields/index.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, form=form),
    )


@router.post("/")
async def store(
    request: Request,
    form_id: int = Form(...),
    field_name: str = Form(...),
    label: str = Form(...),
    form_control: str = Form("input"),
    options: str = Form(""),
    attributes: str = Form(""),
    sort_order: int = Form(0),
    is_required: bool = Form(False),
    is_list_visible: bool = Form(True),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    field_name = field_name.lower().strip()
    if not _FIELD_NAME_RE.match(field_name):
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "字段名只能包含小写字母、数字和下划线"})
        return RedirectResponse(url=url_for("form_fields_index", form_id=form_id), status_code=302)
    if form_control not in _CONTROL_TYPES:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "无效的表单控件类型"})
        return RedirectResponse(url=url_for("form_fields_index", form_id=form_id), status_code=302)
    if db.query(FormField).filter(FormField.form_id == form.id, FormField.field_name == field_name).first():
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "该字段已存在"})
        return RedirectResponse(url=url_for("form_fields_index", form_id=form_id), status_code=302)
    max_order = db.query(func.max(FormField.sort_order)).filter(FormField.form_id == form.id).scalar() or 0
    sort_order = sort_order if sort_order > 0 else int(max_order) + 1
    field = FormField(
        form_id=form.id,
        field_name=field_name,
        label=label,
        form_control=form_control,
        options=options or None,
        attributes=attributes or None,
        sort_order=sort_order,
        is_required=bool(is_required),
        is_list_visible=bool(is_list_visible),
    )
    db.add(field)
    db.commit()
    db.refresh(field)
    _add_column_to_table(form.table_name, field)
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "添加成功", "data": {"id": field.id}})
    return RedirectResponse(url=url_for("form_fields_index", form_id=form_id), status_code=302)


@router.put("/field/{field_id}")
async def update(
    request: Request,
    field_id: int,
    label: str = Form(...),
    form_control: str = Form("input"),
    options: str = Form(""),
    attributes: str = Form(""),
    sort_order: int = Form(0),
    is_required: bool = Form(False),
    is_list_visible: bool = Form(True),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    field = db.query(FormField).filter(FormField.id == field_id).first()
    if not field:
        raise HTTPException(404)
    if form_control not in _CONTROL_TYPES:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "无效的表单控件类型"})
        return RedirectResponse(url=url_for("form_fields_index", form_id=field.form_id), status_code=302)
    old_required = field.is_required
    field.label = label
    field.form_control = form_control
    field.options = options or None
    field.attributes = attributes or None
    field.sort_order = sort_order
    field.is_required = bool(is_required)
    field.is_list_visible = bool(is_list_visible)
    db.commit()
    if old_required != field.is_required:
        try:
            _modify_column_nullable(field.form.table_name, field.field_name, field.is_required)
        except Exception as e:
            return JSONResponse({"code": 1, "msg": f"数据库列约束同步失败: {e}"}, status_code=500)
    return JSONResponse({"code": 0, "msg": "更新成功"})


@router.delete("/field/{field_id}")
async def destroy(
    request: Request,
    field_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "delete")),
    db: Session = Depends(get_db),
):
    field = db.query(FormField).filter(FormField.id == field_id).first()
    if not field:
        raise HTTPException(404)
    form = field.form
    _drop_column_from_table(form.table_name, field.field_name)
    db.delete(field)
    db.commit()
    return JSONResponse({"code": 0, "msg": "删除成功"})
