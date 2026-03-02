# LayCMS

基于 **FastAPI** + **SQLAlchemy** + **Jinja2** 的动态表单 CMS 系统，支持表单分组、表单、表单字段、数据表管理、用户与权限控制。

## 功能概览

- **表单分组**：按业务模块组织表单
- **表单与字段**：可视化创建表单及字段，自动建表
- **支持的控件类型**：`input`、`textarea`、`select`、`radio`、`checkbox`、`date`、`datetime`、`number`、`file`、`editor`、`relation`
- **关联字段**：表单之间可建立关联（外键），支持下拉搜索
- **用户与权限**：用户组、权限配置、表级读写删权限
- **数据管理**：列表、搜索、新增、编辑、删除
- **智能搜索**：普通字符串全字段 OR 模糊查询；输入 `where 条件` 可写自定义条件（防 SQL 注入）

## 技术栈

| 组件 | 说明 |
|------|------|
| FastAPI | Web 框架 |
| SQLAlchemy 2.x | ORM |
| Jinja2 | 模板引擎 |
| MySQL | 数据库 |
| Bootstrap 5 | 前端 UI |
| CKEditor | 富文本编辑 |

## 项目结构

```
laycms/
├── main.py                 # FastAPI 入口
├── .env                    # 环境变量（数据库、密钥等）
├── requirements.txt
├── sql/
│   └── laycms.sql         # 数据库导出脚本
├── app_py/
│   ├── config.py          # 配置
│   ├── database.py        # 数据库连接
│   ├── dependencies.py    # 依赖注入（登录、权限）
│   ├── utils.py           # 工具函数
│   ├── models/            # ORM 模型
│   │   ├── base.py
│   │   ├── form.py        # 表单、表单分组、表单字段
│   │   ├── form_relation.py
│   │   ├── cms_user.py    # 用户
│   │   ├── user_group.py  # 用户组
│   │   └── ...
│   └── routers/           # 路由
│       ├── auth.py        # 登录 / 登出
│       ├── dashboard.py   # 首页
│       ├── form_groups.py # 表单分组
│       ├── forms.py       # 表单
│       ├── form_fields.py # 表单字段
│       ├── form_relations.py # 表单关联
│       ├── table_data.py  # 数据表 CRUD
│       ├── upload.py      # 文件上传
│       ├── users.py       # 用户管理
│       ├── user_groups.py # 用户组
│       └── permissions.py # 权限
├── templates/             # Jinja2 模板
├── public/
│   ├── css/
│   └── upload/            # 上传文件
└── storage/               # Laravel 兼容目录（可选）
```

## 安装与运行

### 1. 环境要求

- Python 3.10+
- MySQL 8.x
- Node.js（如使用前端构建工具）

### 2. 安装依赖

```bash
cd laycms
pip install -r requirements.txt
```

### 3. 配置环境变量

复制 `.env.example` 为 `.env`（如无则手动创建），配置：

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laycms
DB_USERNAME=root
DB_PASSWORD=your_password

SECRET_KEY=your-secret-key-for-session
```

### 4. 初始化数据库

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS laycms DEFAULT CHARSET utf8mb4;"
mysql -u root -p laycms < sql/laycms.sql
```

### 5. 启动服务

```bash
python main.py
# 或
uvicorn main:app --reload --host 0.0.0.0 --port 8000
```

访问：<http://127.0.0.1:8000>

## 主要路由

| 路径 | 说明 |
|------|------|
| `/` | 仪表盘 |
| `/login` | 登录 |
| `/form-groups` | 表单分组管理 |
| `/forms` | 表单管理 |
| `/forms/{id}/form-fields` | 表单字段管理 |
| `/form-relations` | 表单关联配置 |
| `/table-data/{tableName}` | 数据表列表/新增/编辑 |
| `/users` | 用户管理 |
| `/user-groups` | 用户组 |
| `/permissions` | 权限配置 |

## 数据库说明

- 核心表：`cms_forms`、`cms_form_fields`、`cms_form_groups`、`cms_form_relations`
- 用户与权限：`cms_users`、`user_groups`、`group_permissions`
- 业务表：根据表单配置动态创建（如 `school`、`class` 等）

## 数据列表搜索

- **普通字符串**：对当前表所有可见字段进行 OR 模糊匹配（`LIKE '%keyword%'`）
- **where 条件**：以 `where ` 开头时解析为自定义条件，如 `where name='张三' and status=1`、`where id in (1,2,3)`
- 支持操作符：`=`、`!=`、`<`、`>`、`<=`、`>=`、`like`、`in`
- 需登录后才能查询

## 导出数据库

```bash
mysqldump -h 127.0.0.1 -P 3306 -u root -p laycms > sql/laycms.sql
```

## License

MIT
