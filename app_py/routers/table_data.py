import json
import re
from datetime import datetime
from typing import Optional

from fastapi import APIRouter, Depends, Request, HTTPException, Query
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy import text
from sqlalchemy.orm import Session

from app_py.database import get_db, db_execute_raw, db_fetchall, db_fetchone, table_exists, engine
from app_py.dependencies import require_auth, require_table_permission
from app_py.models import CmsUser, Form
from app_py.templating import templates, get_context
from app_py.utils import url_for, json_serializable

router = APIRouter()
_COLUMN_RE = re.compile(r"^[a-z][a-z0-9_]*$")


@router.get("/relation-options")
async def relation_options(
    request: Request,
    table: str = Query(...),
    ref: str = Query(...),
    display: str = Query(...),
    q: Optional[str] = Query(None, max_length=200),
    current_user: CmsUser = Depends(require_auth),
    db: Session = Depends(get_db),
):
    if not _COLUMN_RE.match(ref) or not _COLUMN_RE.match(display) or not _COLUMN_RE.match(table):
        return JSONResponse({"data": []})
    if not db.query(Form).filter(Form.table_name == table).first():
        return JSONResponse({"data": []})
    if not table_exists(table):
        return JSONResponse({"data": []})
    q = (q or "").strip()
    pat = f"%{q}%"
    if q:
        rows = db_fetchall(
            f"SELECT `{ref}`, `{display}` FROM `{table}` WHERE `{display}` LIKE :pat OR `{ref}` LIKE :pat ORDER BY id LIMIT 50",
            {"pat": pat},
        )
    else:
        rows = db_fetchall(
            f"SELECT `{ref}`, `{display}` FROM `{table}` ORDER BY id LIMIT 50",
            {},
        )
    data = [{"value": r[0], "label": r[1] if r[1] is not None else str(r[0])} for r in rows]
    return JSONResponse(json_serializable({"data": data}))


@router.get("/{table_name}", response_class=HTMLResponse)
async def index(
    request: Request,
    table_name: str,
    search: Optional[str] = Query(None),
    page: int = Query(1, ge=1),
    limit: int = Query(15, ge=1, le=100),
    current_user: CmsUser = Depends(require_table_permission("read")),
    db: Session = Depends(get_db),
):
    from sqlalchemy.orm import joinedload
    form = db.query(Form).options(joinedload(Form.fields)).filter(Form.table_name == table_name).first()
    if not form:
        raise HTTPException(404)
    if not table_exists(table_name):
        raise HTTPException(404)
    offset = (page - 1) * limit
    where_clause = ""
    params = {}
    if search:
        search_fields = [f for f in form.fields if f.is_list_visible and f.form_control in ("input", "textarea", "editor")]
        if search_fields:
            conds = " OR ".join([f"`{f.field_name}` LIKE :s{i}" for i, f in enumerate(search_fields)])
            where_clause = f" WHERE ({conds})"
            for i, f in enumerate(search_fields):
                params[f"s{i}"] = f"%{search}%"
    count_sql = f"SELECT COUNT(*) FROM `{table_name}`{where_clause}"
    total = db_fetchone(count_sql, params)[0]
    data_sql = f"SELECT * FROM `{table_name}`{where_clause} ORDER BY id DESC LIMIT :lim OFFSET :off"
    params["lim"] = limit
    params["off"] = offset
    rows = db_fetchall(data_sql, params)
    columns = [c[0] for c in db_fetchall("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t ORDER BY ordinal_position", {"t": table_name})]
    items = [dict(zip(columns, r)) for r in rows]
    if "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest" or page > 1:
        return JSONResponse(json_serializable({"code": 0, "count": total, "data": items, "msg": ""}))
    from app_py.dependencies import get_menu_form_groups
    menu = get_menu_form_groups(db, current_user)
    list_fields_data = [
        {"name": f.field_name, "control": f.form_control, "opts": f.get_options_array()}
        for f in form.fields if f.is_list_visible
    ]
    return templates.TemplateResponse(
        "table_data/index.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, form=form, data=type("Data", (), {"items": items, "total": total})(), list_fields_data=list_fields_data),
    )


@router.get("/{table_name}/create", response_class=HTMLResponse)
async def create(
    request: Request,
    table_name: str,
    current_user: CmsUser = Depends(require_table_permission("create")),
    db: Session = Depends(get_db),
):
    from app_py.models import FormField, FormRelation
    from sqlalchemy.orm import joinedload
    form = db.query(Form).options(
        joinedload(Form.fields)
        .joinedload(FormField.relation)
        .joinedload(FormRelation.related_form)
        .joinedload(Form.fields),
    ).filter(Form.table_name == table_name).first()
    if not form:
        raise HTTPException(404)
    from app_py.dependencies import get_menu_form_groups
    menu = get_menu_form_groups(db, current_user)
    row = None
    return templates.TemplateResponse(
        "table_data/form.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, form=form, tableName=table_name, row=row),
    )


def _validate_required(form, form_data, data) -> Optional[str]:
    """校验必填字段，返回错误信息或 None"""
    for field in form.fields:
        if not field.is_required:
            continue
        if field.form_control == "checkbox":
            val = form_data.getlist(field.field_name + "[]")
            val = json.dumps(val) if isinstance(val, (list, tuple)) else "[]"
            if val == "[]":
                return f"{field.label}不能为空"
        else:
            val = form_data.get(field.field_name)
            if val is None or (isinstance(val, str) and not val.strip()):
                return f"{field.label}不能为空"
    return None


@router.post("/{table_name}")
@router.put("/{table_name}")
async def store(
    request: Request,
    table_name: str,
    current_user: CmsUser = Depends(require_table_permission("create")),
    db: Session = Depends(get_db),
):
    from sqlalchemy.orm import joinedload
    form = db.query(Form).options(joinedload(Form.fields)).filter(Form.table_name == table_name).first()
    if not form:
        raise HTTPException(404)
    data = {}
    form_data = await request.form()
    err = _validate_required(form, form_data, data)
    if err:
        is_ajax = "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest"
        if is_ajax:
            return JSONResponse({"code": 1, "msg": err}, status_code=400)
        request.session["errors"] = [err]
        return RedirectResponse(url=url_for("table_data_create", table_name=table_name), status_code=302)
    for field in form.fields:
        if field.form_control == "checkbox":
            val = form_data.getlist(field.field_name + "[]")
            val = json.dumps(val) if isinstance(val, (list, tuple)) else "[]"
        else:
            val = form_data.get(field.field_name)
        if field.form_control in ("number", "relation"):
            val = int(val) if val is not None and str(val).strip() and str(val).replace("-", "").isdigit() else (None if val in ("", None) else val)
        if val is not None and val != "":
            data[field.field_name] = val
    data["created_at"] = datetime.now()
    data["updated_at"] = datetime.now()
    cols = ", ".join([f"`{k}`" for k in data.keys()])
    placeholders = ", ".join([f":{k}" for k in data.keys()])
    with engine.connect() as conn:
        conn.execute(text(f"INSERT INTO `{table_name}` ({cols}) VALUES ({placeholders})"), data)
        conn.commit()
    if "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest":
        return JSONResponse({"code": 0, "msg": "添加成功"})
    request.session["success"] = "添加成功"
    return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)


@router.get("/{table_name}/{id}/edit", response_class=HTMLResponse)
async def edit(
    request: Request,
    table_name: str,
    id: int,
    current_user: CmsUser = Depends(require_table_permission("update")),
    db: Session = Depends(get_db),
):
    from app_py.models import FormField, FormRelation
    from sqlalchemy.orm import joinedload
    form = db.query(Form).options(
        joinedload(Form.fields)
        .joinedload(FormField.relation)
        .joinedload(FormRelation.related_form)
        .joinedload(Form.fields),
    ).filter(Form.table_name == table_name).first()
    if not form:
        raise HTTPException(404)
    row = db_fetchone(f"SELECT * FROM `{table_name}` WHERE id = :id", {"id": id})
    if not row:
        raise HTTPException(404)
    columns = [c[0] for c in db_fetchall("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t ORDER BY ordinal_position", {"t": table_name})]
    row = dict(zip(columns, row))
    from app_py.dependencies import get_menu_form_groups
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "table_data/form.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, form=form, tableName=table_name, row=row),
    )


@router.put("/{table_name}/{id}")
@router.post("/{table_name}/{id}")
async def update(
    request: Request,
    table_name: str,
    id: int,
    current_user: CmsUser = Depends(require_table_permission("update")),
    db: Session = Depends(get_db),
):
    from sqlalchemy.orm import joinedload
    form = db.query(Form).options(joinedload(Form.fields)).filter(Form.table_name == table_name).first()
    if not form:
        raise HTTPException(404)
    form_data = await request.form()
    err = _validate_required(form, form_data, {})
    if err:
        is_ajax = "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest"
        if is_ajax:
            return JSONResponse({"code": 1, "msg": err}, status_code=400)
        request.session["errors"] = [err]
        return RedirectResponse(url=url_for("table_data_edit", table_name=table_name, id=id), status_code=302)
    data = {}
    for field in form.fields:
        if field.form_control == "checkbox":
            val = form_data.getlist(field.field_name + "[]")
            val = json.dumps(val) if isinstance(val, (list, tuple)) else "[]"
        else:
            val = form_data.get(field.field_name)
        if field.form_control in ("number", "relation"):
            val = int(val) if val is not None and str(val).strip() and str(val).replace("-", "").isdigit() else (None if val in ("", None) else val)
        if val is not None and val != "":
            data[field.field_name] = val
    data["updated_at"] = datetime.now()
    set_clause = ", ".join([f"`{k}` = :{k}" for k in data.keys()])
    data["_id"] = id
    with engine.connect() as conn:
        conn.execute(text(f"UPDATE `{table_name}` SET {set_clause} WHERE id = :_id"), data)
        conn.commit()
    if "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest":
        return JSONResponse({"code": 0, "msg": "更新成功"})
    request.session["success"] = "更新成功"
    return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)


@router.delete("/{table_name}")
async def destroy_collection(
    request: Request,
    table_name: str,
    current_user: CmsUser = Depends(require_table_permission("delete")),
):
    """DELETE 缺少 id 时返回明确错误，避免 405"""
    if "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest":
        return JSONResponse({"code": 1, "msg": "删除需要指定记录 ID，请使用 DELETE /table-data/{table_name}/{id}"}, status_code=400)
    return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)


@router.delete("/{table_name}/{id}")
async def destroy(
    request: Request,
    table_name: str,
    id: int,
    current_user: CmsUser = Depends(require_table_permission("delete")),
    db: Session = Depends(get_db),
):
    db_execute_raw(f"DELETE FROM `{table_name}` WHERE id = :id", {"id": id})
    if "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest":
        return JSONResponse({"code": 0, "msg": "删除成功"})
    return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)
