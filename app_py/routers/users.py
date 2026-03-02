from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.dependencies import require_permission, get_menu_form_groups
from sqlalchemy.orm import joinedload
from app_py.models import CmsUser, UserGroup
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()


@router.get("/", response_class=HTMLResponse)
async def index(
    request: Request,
    current_user: CmsUser = Depends(require_permission("users", "read")),
    db: Session = Depends(get_db),
):
    users = db.query(CmsUser).options(joinedload(CmsUser.user_group)).order_by(CmsUser.id).all()
    groups = db.query(UserGroup).order_by(UserGroup.id).all()
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "users/index.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, users=users, groups=groups),
    )


@router.post("/")
async def store(
    request: Request,
    username: str = Form(...),
    password: str = Form(...),
    nickname: str = Form(""),
    user_group_id: int = Form(...),
    is_active: bool = Form(True),
    current_user: CmsUser = Depends(require_permission("users", "create")),
    db: Session = Depends(get_db),
):
    if db.query(CmsUser).filter(CmsUser.username == username).first():
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "用户名已存在"})
        return RedirectResponse(url=url_for("users_index"), status_code=302)
    if len(password) < 6:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "密码至少6位"})
        return RedirectResponse(url=url_for("users_index"), status_code=302)
    user = CmsUser(
        username=username,
        nickname=nickname or None,
        user_group_id=user_group_id,
        is_active=bool(is_active),
        is_root=False,
    )
    user.set_password(password)
    db.add(user)
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "用户创建成功"})
    return RedirectResponse(url=url_for("users_index"), status_code=302)


@router.put("/{user_id}")
async def update(
    request: Request,
    user_id: int,
    nickname: str = Form(""),
    user_group_id: int = Form(...),
    is_active: bool = Form(True),
    password: str = Form(""),
    current_user: CmsUser = Depends(require_permission("users", "update")),
    db: Session = Depends(get_db),
):
    user = db.query(CmsUser).filter(CmsUser.id == user_id).first()
    if not user:
        raise HTTPException(404)
    if user.is_root:
        return JSONResponse({"code": 1, "msg": "不能修改根用户"}, status_code=403)
    if password:
        if len(password) < 6:
            return JSONResponse({"code": 1, "msg": "密码至少6位"}, status_code=400)
        user.set_password(password)
    user.nickname = nickname or None
    user.user_group_id = user_group_id
    user.is_active = bool(is_active)
    db.commit()
    return JSONResponse({"code": 0, "msg": "更新成功"})


@router.delete("/{user_id}")
async def destroy(
    request: Request,
    user_id: int,
    current_user: CmsUser = Depends(require_permission("users", "delete")),
    db: Session = Depends(get_db),
):
    user = db.query(CmsUser).filter(CmsUser.id == user_id).first()
    if not user:
        raise HTTPException(404)
    if user.is_root:
        return JSONResponse({"code": 1, "msg": "不能删除根用户"}, status_code=403)
    db.delete(user)
    db.commit()
    return JSONResponse({"code": 0, "msg": "删除成功"})
