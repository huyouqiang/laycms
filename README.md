# LayCMS

无代码/低代码风格的**动态表单与数据管理**后台：按业务分组维护表单与字段，自动映射 MySQL 物理表，支持字段关联、用户组与**表级**读写删权限。

当前仓库的**主运行方式**为 **Go + Gin** 后台与 **Layui** 管理界面（`go run ./cmd/laycms`）。`templates/` 中仍保留部分 **Jinja2 + Bootstrap** 页面与 `main.py`，与历史 FastAPI 栈对应；若缺少 `app_py/` 目录则无法直接启动 Python 版，仅作参考。

---

## 功能概览

| 模块 | 说明 |
|------|------|
| 表单分组 | 业务模块划分，`cms_form_groups` |
| 表单与字段 | 创建表单、维护字段、动态加列；控件含 `input`、`textarea`、`select`、`radio`、`checkbox`、`date`、`datetime`、`number`、`file`、`editor`、`relation` |
| 表单关联 | 外键式关联，列表/编辑中下拉检索关联表 |
| 用户与权限 | 用户组、`cms_group_permissions`；系统资源 `_forms` / `_users`；业务表按表名授权 |
| 动态数据 | `/table-data/{table_name}` 列表（Layui Table：分页、搜索、固定 ID/操作列等）、新增、编辑、删除 |
| 上传 | `POST /api/upload`，文件存 `public/upload` |
| 搜索 | 普通关键字：对「列表可见」字段 `OR LIKE`；或以 `where ` 开头写受限条件（防注入） |

---

## 技术栈（Gin 版）

| 组件 | 说明 |
|------|------|
| Go 1.23+ | 语言 |
| Gin | HTTP 框架 |
| GORM + MySQL Driver | 元数据（表单、用户等）；动态表数据多用原生 SQL |
| gin-contrib/sessions + cookie Store | 登录会话 |
| `html/template` | 服务端模板；布局 `layout_admin.html`，页面多为 `templates/fragments/*.html` |
| Layui 2.9.x | 后台 UI（CDN：`unpkg.com/layui`，**脚本路径为** `dist/layui.js`） |
| MySQL 8.x | 数据库，`parseTime=True` 建议开启（DSN 已带） |

---

## 目录结构

```
laycms/
├── cmd/laycms/main.go       # Gin 入口，默认监听 :8000（可用环境变量 PORT）
├── go.mod / go.sum
├── .env                     # 环境变量（见下），勿提交密钥
├── internal/
│   ├── config/              # godotenv、DSN、静态目录、调试开关
│   ├── db/                  # GORM 连接
│   ├── handlers/            # 路由与业务：auth、forms、table_data、users、permissions…
│   ├── menu/                # 侧栏菜单
│   ├── models/              # Form、FormField、CmsUser、GroupPermission 等
│   ├── rawsql/              # 动态表安全查询、FetchAllMaps
│   ├── tabledata/           # where 条件安全解析
│   └── tmpl/                # 布局/片段渲染、字段 HTML、URL 辅助
├── templates/
│   ├── layout_admin.html    # Layui 后台壳
│   ├── fragments/           # Gin 渲染的页面片段（仪表盘、表单、数据列表…）
│   ├── *_extra*.html        # 历史 Jinja/Bootstrap 页面（可选）
│   └── ...
├── public/
│   ├── css/app.css          # 后台定制样式（表格、导航等）
│   └── upload/              # 上传文件
├── sql/
│   ├── laycms.sql           # 库表结构/导出
│   ├── oa_seed.sql          # 可选 Demo
│   └── restaurant_seed.sql  # 可选 Demo
├── main.py / requirements.txt   # 历史 FastAPI 入口（依赖 app_py，当前仓库可能不完整）
└── README.md
```

### 模型与命名注意

- `Form` 结构体中业务表名字段为 **`DataTable`**（GORM 列名 **`table_name`**），避免与 `TableName()` 方法冲突。
- `GroupPermission` 中表名字段为 **`ResourceTable`**（列名仍可为 `table_name`，视迁移而定）。

---

## 环境变量

通过项目根目录 `.env` 加载（`internal/config`）：

| 变量 | 说明 | 默认 |
|------|------|------|
| `APP_NAME` | 应用名 | LayCMS |
| `APP_DEBUG` | `true` 时每请求重读片段模板；生产建议 `false` | true |
| `SECRET_KEY` | Session 加密密钥 | （内置占位，务必修改） |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | MySQL | 127.0.0.1:3306 / laycms / root / 空 |
| `PORT` | HTTP 端口（仅 Gin 启动时） | 8000 |

DSN 已包含 `parseTime=True&loc=Local`，利于 `DATETIME` 在 JSON 中正确格式化。

---

## 安装与运行（Gin）

### 1. 要求

- Go 1.23+
- MySQL 8.x

### 2. 初始化数据库

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS laycms DEFAULT CHARSET utf8mb4;"
mysql -u root -p laycms < sql/laycms.sql
```

可选 Demo：`sql/oa_seed.sql`、`sql/restaurant_seed.sql`。

### 3. 配置 `.env`

至少配置数据库与 `SECRET_KEY`（示例见上文表格）。

### 4. 启动

```bash
cd laycms
go run ./cmd/laycms
```

默认：<http://127.0.0.1:8000> 。若拉取依赖慢可设置：  
`export GOPROXY=https://goproxy.cn,direct`

---

## 主要 HTTP 路由（Gin）

| 路径 | 说明 |
|------|------|
| `GET /` | 仪表盘（需登录） |
| `GET/POST /login` | 登录 |
| `GET /logout` | 登出 |
| `/form-groups` | 表单分组 |
| `/forms`、`/form-fields/{form_id}` | 表单与字段 |
| `POST /form-relations` 等 | 关联配置 |
| `GET /table-data/{table_name}` | 数据列表（HTML）；`Accept: application/json` 或 `X-Requested-With: XMLHttpRequest` 时返回 Layui Table 所需 JSON |
| `GET/POST /table-data/{table_name}/create`、`…/edit` | 新增/编辑 |
| `DELETE /table-data/{table_name}/{id}` | 删除单条（路由需先于无 id 的 DELETE 注册） |
| `/users`、`/user-groups`、`/permissions/{group_id}` | 用户与权限 |
| `POST /api/upload` | 上传 |

---

## 数据列表说明（Layui）

- 列表页使用 **`layui.table.render`**，接口返回 `{ code: 0, count, data, msg }`。
- 左侧固定 **ID** 列；右侧固定 **操作**；动态列由表单「列表可见」字段生成，**主键 `id` 不会在动态列重复出现**。
- 日期时间等类型在服务端 **`normalizeCellForJSON`** 中统一为字符串，避免前端无法展示。

---

## 数据库摘要

- 元数据：`cms_forms`、`cms_form_fields`、`cms_form_groups`、`cms_form_relations`
- 账号权限：`cms_users`、`user_groups`、`cms_group_permissions`（命名以 `sql/laycms.sql` 为准）
- 业务表：随表单配置创建，字段由表单定义同步到 MySQL

---

## 导出数据库

```bash
mysqldump -h 127.0.0.1 -P 3306 -u root -p laycms > sql/laycms.sql
```

---

## License

MIT
