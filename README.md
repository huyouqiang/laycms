# LayCMS - 动态表单 CMS 系统

基于 PHP 8 + Laravel 10 + Bootstrap 5 + jQuery + MySQL 的轻量级 CMS，仿照 DiliCMS 设计，支持动态表单与字段配置、数据 CRUD、表单分组、关联表单（外键）、索引管理、用户权限管理。

## 功能特性

### 表单管理

- **表单分组**：表单必须属于一个分组，分组在左侧菜单栏展示，支持展开/收起
- **表单 CRUD**：新建、编辑、删除表单；创建表单时自动生成对应数据表
- **字段配置**：支持多种表单控件类型
  - `input` - 单行文本
  - `textarea` - 多行文本
  - `number` - 数字
  - `date` / `datetime` - 日期、日期时间
  - `select` / `radio` / `checkbox` - 下拉、单选、多选（需配置 options JSON）
  - `file` - 文件上传
  - `editor` - 富文本（CKEditor 4）
  - `relation` - 关联表单（外键选择）
- **关联表单**：在编辑表单页可配置表间外键关联
  - 选择本表字段或新建字段
  - 选择关联表单及关联表中的任意字段
  - 自动创建 MySQL 外键约束
- **索引管理**：在编辑表单页独立管理数据表索引
  - 查看已添加的索引（索引名称、对应字段、索引类型）
  - 为任意字段添加 MySQL 索引
  - 删除索引（主键除外）

### 数据管理

- **动态 CRUD**：每张数据表支持增删改查
- **搜索**：基于可列表显示字段的模糊搜索
- **分页**：每页 50 条，最多显示 10 个页码按钮，支持首页/上一页/下一页/尾页
- **关联选择**：`relation` 类型字段渲染为可搜索下拉框，选项来自关联表
- **富文本**：`editor` 类型使用 CKEditor 4，列表页显示前 20 字符预览
- **表格滚动**：字段过多时在表格区域内横向滚动

### 用户与权限

- **根用户**：`is_root=true` 拥有全部权限，无需配置
- **用户组**：创建用户组，配置组内权限
- **用户**：创建用户并归属用户组，支持启用/禁用
- **角色显示**：导航栏用户下拉框显示当前用户角色（用户组名称）
- **权限配置**：按表粒度配置增删改查
  - `_forms` - 表单管理（表单、字段、分组、关联）
  - `_users` - 用户管理（用户组、用户、权限）
  - **数据表名**（如 `class`）- 对应表的数据操作权限

## 技术栈

- **后端**：Laravel 10、PHP 8.1+
- **前端**：Bootstrap 5、jQuery、仿 Element UI 风格
- **富文本**：CKEditor 4（CDN）
- **数据库**：MySQL 5.7+

## 环境要求

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Laravel 10.x

## 安装

### 方式一：迁移 + 填充（全新安装）

```bash
# 1. 进入项目目录
cd laycms

# 2. 安装依赖
composer install

# 3. 复制环境配置
cp .env.example .env

# 4. 生成应用密钥
php artisan key:generate

# 5. 配置数据库（编辑 .env）
# DB_DATABASE=laycms
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. 执行迁移与填充
php artisan migrate --seed

# 7. 启动开发服务器
php artisan serve
```

### 方式二：导入 SQL（快速还原）

```bash
# 1-5 同上

# 6. 创建数据库并导入
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS laycms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p laycms < sql/laycms.sql

# 7. 启动开发服务器
php artisan serve
```

访问 http://localhost:8000 ，默认账号：`admin` / `admin123`

## 数据库导出

导出 laycms 数据库所有表结构和数据到 `sql/laycms.sql`：

```bash
mysqldump -h 127.0.0.1 -u root -p laycms --routines --triggers --single-transaction > sql/laycms.sql
```

## 目录结构

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php        # 登录/登出
│   │   ├── DashboardController.php   # 首页
│   │   ├── FormController.php        # 表单 CRUD、索引管理
│   │   ├── FormFieldController.php   # 字段 CRUD
│   │   ├── FormGroupController.php   # 表单分组
│   │   ├── FormRelationController.php # 关联表单/外键
│   │   ├── TableDataController.php   # 动态数据 CRUD
│   │   ├── UploadController.php      # 文件上传
│   │   ├── UserController.php        # 用户管理
│   │   ├── UserGroupController.php   # 用户组
│   │   └── PermissionController.php  # 权限配置
│   └── Middleware/
│       ├── CmsAuth.php               # 认证
│       ├── CheckPermission.php       # 权限校验
│       └── CheckTablePermission.php  # 数据表权限
├── Models/
│   ├── CmsUser.php
│   ├── Form.php
│   ├── FormField.php
│   ├── FormGroup.php
│   ├── FormRelation.php
│   ├── GroupPermission.php
│   └── UserGroup.php
database/
├── migrations/                       # 数据库迁移
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── layouts/app.blade.php             # 主布局（顶栏、侧边栏、主内容）
├── login.blade.php
├── dashboard.blade.php
├── forms/                            # 表单管理（含索引管理）
├── form-groups/                      # 表单分组
├── form-fields/                      # 字段配置
├── table-data/                       # 数据增删改查
├── users/                            # 用户管理
├── user-groups/                      # 用户组
├── permissions/                      # 权限配置
└── no-permission.blade.php
public/
├── css/app.css                       # 全局样式
└── upload/                           # 上传文件存储目录
sql/
└── laycms.sql                        # 数据库完整导出（表结构+数据）
routes/
└── web.php
```

## 数据库表

| 表名 | 说明 |
|------|------|
| `user_groups` | 用户组 |
| `cms_users` | 后台用户 |
| `cms_form_groups` | 表单分组 |
| `cms_forms` | 表单定义 |
| `cms_form_fields` | 表单字段配置 |
| `cms_form_relations` | 表单关联（外键） |
| `cms_group_permissions` | 用户组权限 |
| `class`、`school` 等 | 动态表单对应的业务数据表 |

## 权限说明

| 表名 | 说明 |
|------|------|
| `_forms` | 表单管理（表单、字段、分组、关联、索引） |
| `_users` | 用户管理（用户组、用户、权限） |
| 数据表名 | 该表数据的增删改查 |

根用户（`is_root=true`）拥有所有权限，无需配置。

## 界面说明

- **导航栏**：首页、表单管理（表单分组 / 表单列表）、用户管理（用户组 / 用户列表）、用户下拉（显示角色、退出登录）
- **侧边栏**：按分组展示表单，点击分组可展开/收起；支持折叠为仅显示首字
- **主内容区**：卡片式布局，表格、表单、弹窗

## License

MIT
