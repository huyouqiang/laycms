from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.dependencies import require_permission, get_menu_form_groups
from sqlalchemy.orm import joinedload
from app_py.models import CmsUser, Form, UserGroup, GroupPermission
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()


@router.get("/{group_id}", response_class=HTMLResponse)
async def edit(
    request: Request,
    group_id: int,
    current_user: CmsUser = Depends(require_permission("users", "read")),
    db: Session = Depends(get_db),
):
    group = db.query(UserGroup).options(joinedload(UserGroup.permissions)).filter(UserGroup.id == group_id).first()
    if not group:
        raise HTTPException(404)
    forms = db.query(Form).order_by(Form.sort_order).all()
    tables = {"_forms": "表单管理", "_users": "用户管理"}
    for f in forms:
        tables[f.table_name] = f.name
    perms = {p.table_name: p for p in group.permissions}
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "permissions/edit.html",
        get_context(request, cms_user=current_user, menu_form_groups=menu, group=group, tables=tables, perms=perms),
    )


def _parse_permissions_form(form_data: dict) -> dict:
    result = {}
    for key, val in form_data.items():
        if key.startswith("permissions[") and "][" in key and key.endswith("]"):
            inner = key[len("permissions["):-1]
            parts = inner.split("][")
            if len(parts) == 2:
                table, action = parts
                if table not in result:
                    result[table] = {}
                result[table][action] = val not in ("", "0", "false", False)
    return result


@router.put("/{group_id}")
async def update(
    request: Request,
    group_id: int,
    current_user: CmsUser = Depends(require_permission("users", "update")),
    db: Session = Depends(get_db),
):
    group = db.query(UserGroup).filter(UserGroup.id == group_id).first()
    if not group:
        raise HTTPException(404)
    form_data = await request.form()
    submitted = _parse_permissions_form(dict(form_data))
    forms = db.query(Form).order_by(Form.sort_order).all()
    all_tables = {"_forms": 1, "_users": 1}
    for f in forms:
        all_tables[f.table_name] = 1
    for table_name in all_tables:
        actions = submitted.get(table_name, {})
        perm = db.query(GroupPermission).filter(
            GroupPermission.user_group_id == group_id,
            GroupPermission.table_name == table_name,
        ).first()
        if not perm:
            perm = GroupPermission(user_group_id=group_id, table_name=table_name)
            db.add(perm)
        perm.can_create = bool(actions.get("create"))
        perm.can_read = bool(actions.get("read"))
        perm.can_update = bool(actions.get("update"))
        perm.can_delete = bool(actions.get("delete"))
    db.commit()
    return JSONResponse({"code": 0, "msg": "权限保存成功"})
