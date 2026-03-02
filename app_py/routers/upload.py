import secrets
from datetime import datetime
from pathlib import Path

from fastapi import APIRouter, Depends, UploadFile, File, HTTPException
from fastapi.responses import JSONResponse

from app_py.config import UPLOAD_DIR
from app_py.dependencies import require_auth
from app_py.models import CmsUser

router = APIRouter()

MAX_SIZE = 10 * 1024 * 1024  # 10MB


@router.post("/upload")
async def store(
    file: UploadFile = File(...),
    current_user: CmsUser = Depends(require_auth),
):
    ext = Path(file.filename or "").suffix
    name = f"{datetime.now().strftime('%Y%m%d%H%M%S')}_{secrets.token_hex(3)}{ext}"
    path = UPLOAD_DIR / name
    UPLOAD_DIR.mkdir(parents=True, exist_ok=True)
    content = await file.read()
    if len(content) > MAX_SIZE:
        raise HTTPException(400, detail={"code": 1, "msg": "文件大小不能超过 10MB"})
    path.write_bytes(content)
    return JSONResponse({"code": 0, "msg": "上传成功", "path": f"upload/{name}"})
