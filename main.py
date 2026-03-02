"""
LayCMS - FastAPI 动态表单 CMS 系统
"""
from fastapi import FastAPI, Request
from fastapi.responses import RedirectResponse
from fastapi.staticfiles import StaticFiles
from starlette.middleware.sessions import SessionMiddleware

from app_py.config import SECRET_KEY, STATIC_DIR, UPLOAD_DIR
from app_py.dependencies import RedirectException
from app_py.routers import auth, dashboard, form_groups, forms, form_relations, form_fields
from app_py.routers import table_data, upload, users, user_groups, permissions


# 确保上传目录存在
UPLOAD_DIR.mkdir(parents=True, exist_ok=True)

app = FastAPI(title="LayCMS")


@app.exception_handler(RedirectException)
async def redirect_exception_handler(request: Request, exc: RedirectException):
    return RedirectResponse(url=exc.url, status_code=exc.status_code)


app.add_middleware(SessionMiddleware, secret_key=SECRET_KEY, max_age=120*60)  # 120 min

# 静态文件
app.mount("/upload", StaticFiles(directory=str(UPLOAD_DIR)), name="upload")
app.mount("/css", StaticFiles(directory=str(STATIC_DIR / "css")), name="css")
if (STATIC_DIR / "js").exists():
    app.mount("/js", StaticFiles(directory=str(STATIC_DIR / "js")), name="js")

# 路由
app.include_router(auth.router, tags=["auth"])
app.include_router(dashboard.router, tags=["dashboard"])
app.include_router(form_groups.router, prefix="/form-groups", tags=["form-groups"])
app.include_router(forms.router, prefix="/forms", tags=["forms"])
app.include_router(form_relations.router, prefix="/form-relations", tags=["form-relations"])
app.include_router(form_fields.router, prefix="/form-fields", tags=["form-fields"])
app.include_router(table_data.router, prefix="/table-data", tags=["table-data"])
app.include_router(upload.router, prefix="/api", tags=["upload"])
app.include_router(users.router, prefix="/users", tags=["users"])
app.include_router(user_groups.router, prefix="/user-groups", tags=["user-groups"])
app.include_router(permissions.router, prefix="/permissions", tags=["permissions"])


if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)
