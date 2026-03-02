import hashlib
import re
from fastapi import APIRouter, Depends, Request, Form, HTTPException
from fastapi.responses import RedirectResponse, HTMLResponse, JSONResponse
from sqlalchemy.orm import Session, joinedload

from app_py.database import get_db, db_execute_raw, db_fetchall, db_fetchone, table_exists, get_table_columns
from app_py.dependencies import require_auth, require_permission, get_menu_form_groups
from app_py.models import CmsUser, Form as FormModel, FormGroup, FormField, FormRelation
from app_py.templating import templates, get_context
from app_py.utils import url_for

router = APIRouter()

_TABLE_NAME_RE = re.compile(r"^[a-z][a-z0-9_]*$")


@router.get("/", response_class=HTMLResponse)
async def index(
    request: Request,
    current_user: CmsUser = Depends(require_permission("forms", "read")),
    db: Session = Depends(get_db),
):
    forms = db.query(FormModel).options(
        joinedload(FormModel.form_group),
        joinedload(FormModel.fields),
    ).order_by(FormModel.sort_order).all()
    form_counts = {f.id: len(f.fields) for f in forms}
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "forms/index.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, forms=forms, form_counts=form_counts),
    )


@router.get("/create", response_class=HTMLResponse)
async def create(
    request: Request,
    current_user: CmsUser = Depends(require_permission("forms", "create")),
    db: Session = Depends(get_db),
):
    form_groups = db.query(FormGroup).order_by(FormGroup.sort_order).all()
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "forms/create.html",
        get_context(request, cms_user=current_user, db=db, menu_form_groups=menu, form_groups=form_groups),
    )


@router.post("/")
async def store(
    request: Request,
    name: str = Form(...),
    table_name: str = Form(...),
    description: str = Form(""),
    form_group_id: int = Form(...),
    current_user: CmsUser = Depends(require_permission("forms", "create")),
    db: Session = Depends(get_db),
):
    table_name = table_name.lower().strip()
    if not _TABLE_NAME_RE.match(table_name):
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "表名只能包含小写字母、数字和下划线"})
        return RedirectResponse(url=url_for("forms_create"), status_code=302)
    if db.query(FormModel).filter(FormModel.table_name == table_name).first():
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "表名已存在"})
        return RedirectResponse(url=url_for("forms_create"), status_code=302)
    form = FormModel(name=name, table_name=table_name, description=description or None, form_group_id=form_group_id)
    db.add(form)
    db.commit()
    db.refresh(form)
    sql = (
        f"CREATE TABLE `{table_name}` "
        "(`id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, "
        "`created_at` timestamp NULL, `updated_at` timestamp NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    )
    db_execute_raw(sql)
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "创建成功", "data": {"id": form.id, "name": form.name, "table_name": form.table_name}})
    return RedirectResponse(url=url_for("forms_index"), status_code=302)


def _get_table_indexes(table_name: str) -> list:
    if not table_exists(table_name):
        return []
    rows = db_fetchall(
        "SELECT INDEX_NAME, COLUMN_NAME, INDEX_TYPE, NON_UNIQUE FROM information_schema.STATISTICS "
        "WHERE table_schema = DATABASE() AND table_name = :t ORDER BY INDEX_NAME, SEQ_IN_INDEX",
        {"t": table_name},
    )
    grouped = {}
    for r in rows:
        key = r[0]
        if key not in grouped:
            t = "普通"
            if r[0] == "PRIMARY":
                t = "主键"
            elif r[2] == "FULLTEXT":
                t = "全文"
            elif r[2] == "SPATIAL":
                t = "空间"
            elif int(r[3]) == 0:
                t = "唯一"
            else:
                t = f"普通 ({r[2]})"
            grouped[key] = {"name": r[0], "columns": [], "type": t}
        grouped[key]["columns"].append(r[1])
    return [{"name": g["name"], "column": ", ".join(g["columns"]), "type": g["type"]} for g in grouped.values()]


@router.get("/{form_id}/related-columns")
async def get_related_columns(
    form_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "read")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    if not table_exists(form.table_name):
        return JSONResponse({"columns": []})
    columns = get_table_columns(form.table_name)
    return JSONResponse({"columns": columns})


@router.get("/{form_id}", response_class=HTMLResponse)
async def edit(
    request: Request,
    form_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "read")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).options(
        joinedload(FormModel.fields),
        joinedload(FormModel.relations).joinedload(FormRelation.form_field),
        joinedload(FormModel.relations).joinedload(FormRelation.related_form),
    ).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    form_groups = db.query(FormGroup).order_by(FormGroup.sort_order).all()
    other_forms = db.query(FormModel).filter(FormModel.id != form_id).order_by(FormModel.sort_order).all()
    table_columns = get_table_columns(form.table_name) if table_exists(form.table_name) else []
    table_indexes = _get_table_indexes(form.table_name)
    menu = get_menu_form_groups(db, current_user)
    return templates.TemplateResponse(
        "forms/edit.html",
        get_context(
            request,
            cms_user=current_user,
            db=db,
            menu_form_groups=menu,
            form=form,
            form_groups=form_groups,
            other_forms=other_forms,
            table_columns=table_columns,
            table_indexes=table_indexes,
        ),
    )


@router.put("/{form_id}")
async def update(
    request: Request,
    form_id: int,
    name: str = Form(...),
    table_name: str = Form(...),
    description: str = Form(""),
    sort_order: int = Form(0),
    form_group_id: int = Form(...),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    table_name = table_name.lower().strip()
    if not _TABLE_NAME_RE.match(table_name):
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "表名只能包含小写字母、数字和下划线"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    existing = db.query(FormModel).filter(FormModel.table_name == table_name, FormModel.id != form_id).first()
    if existing:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "表名已存在"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    form.name = name
    form.table_name = table_name
    form.description = description or None
    form.sort_order = sort_order
    form.form_group_id = form_group_id
    db.commit()
    return JSONResponse({"code": 0, "msg": "更新成功"})


@router.post("/{form_id}/add-index")
async def add_index(
    request: Request,
    form_id: int,
    column_name: str = Form(...),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    column_name = column_name.strip()[:64]
    if not table_exists(form.table_name) or column_name not in get_table_columns(form.table_name):
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "字段不存在"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    index_name = f"idx_{form.table_name}_{column_name}"
    if len(index_name) > 64:
        index_name = "idx_" + hashlib.md5(f"{form.table_name}_{column_name}".encode()).hexdigest()[:32]
    existing = db_fetchone(
        "SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1",
        {"t": form.table_name, "c": column_name},
    )
    if existing:
        return JSONResponse({"code": 0, "msg": "该字段已有索引"})
    try:
        db_execute_raw(f"ALTER TABLE `{form.table_name}` ADD INDEX `{index_name}` (`{column_name}`)")
    except Exception as e:
        msg = str(e)
        if "Duplicate" in msg or "already exists" in msg:
            return JSONResponse({"code": 0, "msg": "索引已存在"})
        raise HTTPException(400, detail={"code": 1, "msg": msg})
    return JSONResponse({"code": 0, "msg": "索引添加成功"})


@router.delete("/{form_id}/drop-index")
async def drop_index(
    request: Request,
    form_id: int,
    index_name: str = Form(...),
    current_user: CmsUser = Depends(require_permission("forms", "update")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    index_name = index_name.strip()[:64]
    if index_name == "PRIMARY":
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "不能删除主键索引"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    if not table_exists(form.table_name):
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "数据表不存在"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    existing = db_fetchone(
        "SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :t AND index_name = :idx LIMIT 1",
        {"t": form.table_name, "idx": index_name},
    )
    if not existing:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": "索引不存在"})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    try:
        db_execute_raw(f"ALTER TABLE `{form.table_name}` DROP INDEX `{index_name}`")
    except Exception as e:
        if "application/json" in request.headers.get("accept", ""):
            raise HTTPException(400, detail={"code": 1, "msg": str(e)})
        return RedirectResponse(url=url_for("forms_edit", id=form_id), status_code=302)
    return JSONResponse({"code": 0, "msg": "索引已删除"})


@router.delete("/{form_id}")
async def destroy(
    request: Request,
    form_id: int,
    current_user: CmsUser = Depends(require_permission("forms", "delete")),
    db: Session = Depends(get_db),
):
    form = db.query(FormModel).filter(FormModel.id == form_id).first()
    if not form:
        raise HTTPException(404)
    db_execute_raw(f"DROP TABLE IF EXISTS `{form.table_name}`")
    db.delete(form)
    db.commit()
    if "application/json" in request.headers.get("accept", ""):
        return JSONResponse({"code": 0, "msg": "删除成功"})
    return RedirectResponse(url=url_for("forms_index"), status_code=302)
