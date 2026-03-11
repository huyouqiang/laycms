import json
import re
from datetime import datetime
from typing import Optional

from fastapi import APIRouter, Depends, Request, HTTPException, Query
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy import text
from sqlalchemy.exc import OperationalError, IntegrityError
from sqlalchemy.orm import Session

from app_py.database import get_db, get_table_columns, db_execute_raw, db_fetchall, db_fetchone, table_exists, engine
from app_py.dependencies import require_auth, require_table_permission
from app_py.models import CmsUser, Form
from app_py.templating import templates, get_context
from app_py.utils import url_for, json_serializable

router = APIRouter()
_COLUMN_RE = re.compile(r"^[a-z][a-z0-9_]*$")

# 危险 SQL 关键字，用于 where 解析时的黑名单
_SQL_DANGEROUS = frozenset(
    ";--*/\\union\\select\\drop\\truncate\\insert\\delete\\update\\exec\\alter\\create\\grant\\revoke".split("\\")
)

def _parse_single_condition(
    tok: str, allowed: set, extra_params: dict, param_prefix: str, param_idx: list
) -> Optional[str]:
    """解析单个条件，返回 SQL 片段或 None。"""
    m = re.match(
        r"^\s*([a-z][a-z0-9_]*)\s*(=|\!=|<=|>=|<|>|like|in)\s*(.+)$",
        tok.strip(),
        re.I | re.DOTALL,
    )
    if not m:
        return None
    col, op, val_part = m.group(1), m.group(2).lower(), m.group(3).strip()
    if col not in allowed:
        return None

    def _next():
        key = f"{param_prefix}{param_idx[0]}"
        param_idx[0] += 1
        return key

    if op == "in":
        inn = re.match(r"^\s*\((.+)\)\s*$", val_part, re.DOTALL)
        if not inn:
            return None
        items = re.split(r",\s*", inn.group(1))
        placeholders = []
        for it in items:
            it = it.strip()
            if it.startswith("'") and it.endswith("'"):
                v = it[1:-1].replace("''", "'")
            elif it.startswith('"') and it.endswith('"'):
                v = it[1:-1].replace('""', '"')
            elif it.replace("-", "").replace(".", "").isdigit():
                v = int(it) if "." not in it else float(it)
            else:
                return None
            k = _next()
            placeholders.append(f":{k}")
            extra_params[k] = v
        return f"`{col}` IN (" + ", ".join(placeholders) + ")"
    # = != < > <= >= like
    if val_part.startswith("'") and val_part.endswith("'"):
        v = val_part[1:-1].replace("''", "'")
    elif val_part.startswith('"') and val_part.endswith('"'):
        v = val_part[1:-1].replace('""', '"')
    elif val_part.replace("-", "").replace(".", "").isdigit():
        v = int(val_part) if "." not in val_part else float(val_part)
    elif val_part.upper() == "NULL":
        v = None
    else:
        return None
    k = _next()
    extra_params[k] = v
    if op == "like":
        return f"`{col}` LIKE :{k}"
    if v is None:
        if op == "=":
            return f"`{col}` IS NULL"
        if op == "!=":
            return f"`{col}` IS NOT NULL"
        return None
    return f"`{col}` {op.upper()} :{k}"


def _parse_safe_where(
    raw_where: str,
    allowed_columns: list,
    params: dict,
    param_prefix: str = "w",
) -> tuple[str, dict]:
    """
    解析 where 子句，仅允许白名单列名 + 参数化值，防 SQL 注入。
    支持: col = 'val', col = 123, col like '%val%', col in (1,2,3)
    多条件用 and/or 连接。
    返回 (where_sql, updated_params)，解析失败返回 ("", params)。
    """
    allowed = {c for c in allowed_columns if _COLUMN_RE.match(c)}
    raw = raw_where.strip()
    lower = raw.lower()
    for d in _SQL_DANGEROUS:
        if d in lower:
            return "", params
    if not raw:
        return "", params

    parts = re.split(r"\s+(and|or)\s+", raw, flags=re.I)
    tokens = [parts[i].strip() for i in range(0, len(parts), 2) if i < len(parts)]
    logic = [parts[i].lower() for i in range(1, len(parts), 2) if i < len(parts)]
    if not tokens:
        return "", params

    extra_params = dict(params)
    param_idx = [0]
    conditions = []
    for tok in tokens:
        frag = _parse_single_condition(tok, allowed, extra_params, param_prefix, param_idx)
        if frag is None:
            return "", params
        conditions.append(frag)

    if len(conditions) == 1:
        return conditions[0], extra_params
    result = [conditions[0]]
    for i in range(1, len(conditions)):
        result.append(logic[i - 1] if i - 1 < len(logic) else "and")
        result.append(conditions[i])
    return " ".join(result), extra_params


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
    search: Optional[str] = Query(None, max_length=500),
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
        search = search.strip()
        if search.lower().startswith("where "):
            raw_where = search[6:].strip()
            allowed_cols = get_table_columns(table_name)
            parsed_sql, params = _parse_safe_where(raw_where, allowed_cols, {}, "w")
            if parsed_sql:
                where_clause = f" WHERE ({parsed_sql})"
        else:
            search_fields = [f for f in form.fields if f.is_list_visible]
            if search_fields:
                conds = []
                for i, f in enumerate(search_fields):
                    if f.form_control in ("number", "relation"):
                        conds.append(f"CAST(`{f.field_name}` AS CHAR) LIKE :s{i}")
                    else:
                        conds.append(f"`{f.field_name}` LIKE :s{i}")
                    params[f"s{i}"] = f"%{search}%"
                where_clause = f" WHERE ({' OR '.join(conds)})"
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
    is_json = "application/json" in request.headers.get("accept", "") or request.headers.get("x-requested-with") == "XMLHttpRequest"
    try:
        db_execute_raw(f"DELETE FROM `{table_name}` WHERE id = :id", {"id": id})
    except (OperationalError, IntegrityError) as e:
        msg = str(e.orig) if getattr(e, "orig", None) else str(e)
        if is_json:
            return JSONResponse({"code": 1, "msg": msg}, status_code=400)
        request.session["errors"] = [msg]
        return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)
    if is_json:
        return JSONResponse({"code": 0, "msg": "删除成功"})
    return RedirectResponse(url=url_for("table_data_index", table_name=table_name), status_code=302)
