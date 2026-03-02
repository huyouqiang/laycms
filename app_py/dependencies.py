from typing import Optional
from fastapi import Request, Depends, HTTPException
from fastapi.responses import JSONResponse
from sqlalchemy.orm import Session

from app_py.database import get_db
from app_py.models import CmsUser, Form, FormGroup, FormField
from app_py.utils import url_for


class RedirectException(Exception):
    """Raised when a redirect is needed (e.g. auth required)."""

    def __init__(self, url: str, status_code: int = 302):
        self.url = url
        self.status_code = status_code


def get_current_user(request: Request, db: Session = Depends(get_db)) -> Optional[CmsUser]:
    user_id = request.session.get("cms_user_id")
    if not user_id:
        return None
    user = db.query(CmsUser).filter(CmsUser.id == user_id).first()
    if not user or not user.is_active:
        return None
    return user


def require_auth(request: Request, current_user: Optional[CmsUser] = Depends(get_current_user)):
    if not current_user:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(status_code=401, detail={"code": 401, "msg": "请先登录"})
        raise RedirectException(url_for("login"), status_code=302)
    return current_user


def require_permission(resource: str, action: str):  # resource: forms, users; action: read, create, update, delete, manage
    def _dep(request: Request, current_user: CmsUser = Depends(require_auth), db: Session = Depends(get_db)):
        if current_user.is_root:
            return current_user
        table_map = {"forms": "_forms", "users": "_users"}
        tbl = table_map.get(resource, resource)
        check_action = "read" if action == "manage" else action
        if not current_user.has_permission(tbl, check_action, db):
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(status_code=403, detail={"code": 403, "msg": "无权限"})
            raise RedirectException(url_for("no_permission"), status_code=302)
        return current_user
    return _dep


def require_table_permission(action: str):
    def _dep(
        request: Request,
        current_user: CmsUser = Depends(require_auth),
        db: Session = Depends(get_db),
    ):
        table_name = request.path_params.get("tableName") or request.path_params.get("table_name", "")
        if current_user.is_root:
            return current_user
        if not current_user.can_access_table(table_name, action, db):
            if "application/json" in request.headers.get("accept", ""):
                raise HTTPException(status_code=403, detail={"code": 403, "msg": "无权限"})
            raise RedirectException(url_for("no_permission"), status_code=302)
        return current_user
    return _dep


def get_menu_form_groups(db: Session, current_user: CmsUser):
    groups = db.query(FormGroup).order_by(FormGroup.sort_order).all()
    result = []
    for g in groups:
        forms = [f for f in g.forms if current_user.is_root or current_user.can_access_table(f.table_name, "read", db)]
        if forms:
            result.append({"id": g.id, "name": g.name, "forms": forms})
    return result
