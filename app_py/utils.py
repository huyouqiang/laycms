import secrets
from datetime import date, datetime
from decimal import Decimal
from urllib.parse import urljoin


def json_serializable(obj):
    """递归转换对象为 JSON 可序列化格式（datetime、date、Decimal 等）"""
    if isinstance(obj, (datetime, date)):
        return obj.isoformat()
    if isinstance(obj, Decimal):
        return float(obj)
    if isinstance(obj, dict):
        return {k: json_serializable(v) for k, v in obj.items()}
    if isinstance(obj, (list, tuple)):
        return [json_serializable(v) for v in obj]
    return obj


ROUTES = {
    "login": "/login",
    "logout": "/logout",
    "dashboard": "/",
    "form_groups_index": "/form-groups",
    "form_groups_destroy": "/form-groups/{group_id}",
    "forms_index": "/forms",
    "forms_create": "/forms/create",
    "forms_edit": "/forms/{id}",
    "form_fields_index": "/form-fields/{form_id}",
    "form_fields_store": "/form-fields",
    "form_fields_update": "/form-fields/field/{field_id}",
    "form_fields_destroy": "/form-fields/field/{field_id}",
    "table_data_index": "/table-data/{table_name}",
    "table_data_create": "/table-data/{table_name}/create",
    "table_data_create": "/table-data/{table_name}/create",
    "table_data_edit": "/table-data/{table_name}/{id}/edit",
    "users_index": "/users",
    "user_groups_index": "/user-groups",
    "permissions_edit": "/permissions/{group_id}",
    "no_permission": "/no-permission",
}


def route(name: str, **kwargs) -> str:
    """Alias for url_for for template compatibility."""
    return url_for(name, **kwargs)


def url_for(name: str, **kwargs) -> str:
    path = ROUTES.get(name, "/")
    for k, v in kwargs.items():
        path = path.replace("{" + k + "}", str(v))
    return path


def asset(path: str, request=None) -> str:
    if request:
        base = f"{request.url.scheme}://{request.url.netloc.rstrip('/')}"
        base_path = request.scope.get("root_path", "") or ""
        return f"{base}{base_path}/{path.lstrip('/')}"
    return f"/{path.lstrip('/')}"


def csrf_token(request) -> str:
    if "csrf_token" not in request.session:
        request.session["csrf_token"] = secrets.token_hex(32)
    return request.session["csrf_token"]
