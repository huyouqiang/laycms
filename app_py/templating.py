from datetime import datetime
from fastapi.templating import Jinja2Templates
from app_py.config import TEMPLATES_DIR

templates = Jinja2Templates(directory=str(TEMPLATES_DIR))
templates.env.globals["now"] = datetime.now


def _fromjson_filter(val):
    """解析 JSON 字符串为 Python 对象，用于复选框等"""
    if val is None or val == "":
        return []
    if isinstance(val, (list, dict)):
        return val
    import json
    try:
        return json.loads(val) if isinstance(val, str) else []
    except (ValueError, TypeError):
        return [val] if val else []


templates.env.filters["fromjson"] = _fromjson_filter


def _make_asset_global():
    """向 Jinja2 全局注入 asset，使 {% from %} 引入的宏也可用"""
    from jinja2 import pass_context
    from app_py.utils import asset as asset_fn
    @pass_context
    def asset(ctx, path: str):
        return asset_fn(path, ctx.get("request"))
    templates.env.globals["asset"] = asset


_make_asset_global()


def get_context(request, **kwargs):
    from app_py.utils import url_for, asset, csrf_token, route
    ctx = {
        "request": request,
        "url_for": url_for,
        "route": route,
        "asset": lambda p: asset(p, request),
        "csrf_token": lambda: csrf_token(request),
    }
    cms_user = kwargs.get("cms_user")
    db = kwargs.get("db")
    if cms_user and db:
        ctx["can"] = lambda tbl, act: cms_user.has_permission(tbl, act, db)
        ctx["can_access_table"] = lambda tbl, act: cms_user.can_access_table(tbl, act, db)
    ctx.update(kwargs)
    return ctx
