from fastapi import APIRouter, Request, Depends, Form
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.models import CmsUser
from app_py.utils import url_for
from app_py.templating import templates

router = APIRouter()


@router.get("/login", response_class=HTMLResponse)
async def show_login(request: Request):
    from app_py.templating import get_context
    return templates.TemplateResponse("login.html", get_context(request))


@router.post("/login")
async def login(
    request: Request,
    username: str = Form(...),
    password: str = Form(...),
    db: Session = Depends(get_db),
):
    user = db.query(CmsUser).filter(CmsUser.username == username).first()
    if not user or not user.check_password(password):
        if "application/json" in request.headers.get("accept", ""):
            return JSONResponse({"code": 400, "msg": "用户名或密码错误"})
        from app_py.templating import get_context
        return templates.TemplateResponse("login.html", get_context(request, errors=["用户名或密码错误"], old_username=username))
    if not user.is_active:
        if "application/json" in request.headers.get("accept", ""):
            return JSONResponse({"code": 400, "msg": "账号已被禁用"})
        from app_py.templating import get_context
        return templates.TemplateResponse("login.html", get_context(request, errors=["账号已被禁用"], old_username=username))
    request.session["cms_user_id"] = user.id
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "登录成功", "redirect": url_for("dashboard")})
    return RedirectResponse(url=url_for("dashboard"), status_code=302)


@router.get("/logout")
async def logout(request: Request):
    request.session.pop("cms_user_id", None)
    return RedirectResponse(url=url_for("login"), status_code=302)
