from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.dependencies import require_permission, get_menu_form_groups
from sqlalchemy.orm import joinedload
from app_py.models import CmsUser, UserGroup, GroupPermission
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()


@router.get("/", response_class=HTMLResponse)
async def index(
    request: Request,
    current_user: CmsUser = Depends(require_permission("users", "read")),
    db: Session = Depends(get_db),
):
    groups = db.query(UserGroup).options(joinedload(UserGroup.users)).order_by(UserGroup.id).all()
    group_counts = {g.id: len(g.users) for g in groups}
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "user_groups/index.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, groups=groups, group_counts=group_counts),
    )


@router.post("/")
async def store(
    request: Request,
    name: str = Form(...),
    description: str = Form(""),
    current_user: CmsUser = Depends(require_permission("users", "create")),
    db: Session = Depends(get_db),
):
    group = UserGroup(name=name, description=description or None)
    db.add(group)
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "用户组创建成功"})
    return RedirectResponse(url=url_for("user_groups_index"), status_code=302)


@router.put("/{group_id}")
async def update(
    request: Request,
    group_id: int,
    name: str = Form(...),
    description: str = Form(""),
    current_user: CmsUser = Depends(require_permission("users", "update")),
    db: Session = Depends(get_db),
):
    group = db.query(UserGroup).filter(UserGroup.id == group_id).first()
    if not group:
        raise HTTPException(404)
    group.name = name
    group.description = description or None
    db.commit()
    return JSONResponse({"code": 0, "msg": "更新成功"})


@router.delete("/{group_id}")
async def destroy(
    request: Request,
    group_id: int,
    current_user: CmsUser = Depends(require_permission("users", "delete")),
    db: Session = Depends(get_db),
):
    group = db.query(UserGroup).filter(UserGroup.id == group_id).first()
    if not group:
        raise HTTPException(404)
    if group.users:
        return JSONResponse({"code": 1, "msg": "该用户组下存在用户，无法删除"}, status_code=400)
    db.query(GroupPermission).filter(GroupPermission.user_group_id == group_id).delete()
    db.delete(group)
    db.commit()
    return JSONResponse({"code": 0, "msg": "删除成功"})
