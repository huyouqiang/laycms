from fastapi import APIRouter, Depends, Request
from fastapi.responses import HTMLResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.dependencies import require_auth, get_menu_form_groups
from app_py.models import CmsUser, Form
from app_py.templating import templates, get_context

router = APIRouter()


@router.get("/", response_class=HTMLResponse)
async def index(
    request: Request,
    current_user: CmsUser = Depends(require_auth),
    db: Session = Depends(get_db),
):
    forms = db.query(Form).order_by(Form.sort_order).all()
    menu_form_groups = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse("dashboard.html", get_context(
        request, cms_user=current_user, db=db, menu_form_groups=menu_form_groups, forms=forms
    ))


@router.get("/no-permission", response_class=HTMLResponse)
async def no_permission(request: Request):
    return templates.TemplateResponse("no_permission.html", get_context(request, cms_user=None))
