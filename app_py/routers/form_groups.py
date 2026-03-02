from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.dependencies import require_auth, require_permission, get_menu_form_groups
from app_py.models import CmsUser, FormGroup
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()


@router.get("/", response_class=HTMLResponse)
async def index(request: Request, current_user: CmsUser = Depends(require_permission("forms", "read")), db: Session = Depends(get_db)):
    groups = db.query(FormGroup).order_by(FormGroup.sort_order, FormGroup.id).all()
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse("form_groups/index.html", get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, groups=groups))


@router.post("/")
async def store(request: Request, name: str = Form(...), sort_order: int = Form(0), current_user: CmsUser = Depends(require_permission("forms", "create")), db: Session = Depends(get_db)):
    grp = FormGroup(name=name, sort_order=sort_order)
    db.add(grp)
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "分组创建成功"})
    request.session["success"] = "分组创建成功"
    return RedirectResponse(url=url_for("form_groups_index"), status_code=302)


@router.put("/{group_id}")
async def update(request: Request, group_id: int, name: str = Form(...), sort_order: int = Form(0), current_user: CmsUser = Depends(require_permission("forms", "update")), db: Session = Depends(get_db)):
    grp = db.query(FormGroup).filter(FormGroup.id == group_id).first()
    if not grp:
        raise HTTPException(404)
    grp.name, grp.sort_order = name, sort_order
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "更新成功"})
    return RedirectResponse(url=url_for("form_groups_index"), status_code=302)


@router.delete("/{group_id}")
async def destroy(request: Request, group_id: int, current_user: CmsUser = Depends(require_permission("forms", "delete")), db: Session = Depends(get_db)):
    grp = db.query(FormGroup).filter(FormGroup.id == group_id).first()
    if not grp:
        raise HTTPException(404)
    if grp.forms and len(grp.forms) > 0:
        if "application/json" in request.headers.get("accept", ""):
            return JSONResponse({"code": 1, "msg": "该分组下存在表单，请先移出或删除表单"}, 400)
        return RedirectResponse(url=url_for("form_groups_index"), status_code=302)
    db.delete(grp)
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "删除成功"})
    return RedirectResponse(url=url_for("form_groups_index"), status_code=302)
