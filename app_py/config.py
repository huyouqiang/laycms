import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()

BASE_DIR = Path(__file__).resolve().parent.parent

APP_NAME = os.getenv("APP_NAME", "LayCMS")
DEBUG = os.getenv("APP_DEBUG", "true").lower() == "true"
SECRET_KEY = os.getenv("SECRET_KEY", "laycms-secret-key-change-in-production")

# Database
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_DATABASE = os.getenv("DB_DATABASE", "laycms")
DB_USERNAME = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "")
DATABASE_URL = f"mysql+pymysql://{DB_USERNAME}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_DATABASE}?charset=utf8mb4"

# Paths
UPLOAD_DIR = BASE_DIR / "public" / "upload"
STATIC_DIR = BASE_DIR / "public"
TEMPLATES_DIR = BASE_DIR / "templates"
