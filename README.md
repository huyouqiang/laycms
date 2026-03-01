# LayCMS - 动态表单 CMS 系统

基于 PHP 8 + Laravel 10 + Bootstrap 5 + jQuery + MySQL 的轻量级 CMS，仿照 DiliCMS 设计，支持动态表单与字段配置、数据 CRUD、表单分组、关联表单（外键）、用户权限管理。

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
  - `file` - 文件
  - `editor` - 富文本
  - `relation` - 关联表单（外键选择）
- **关联表单**：在编辑表单页可配置表间外键关联
  - 选择本表字段或新建字段
  - 选择关联表单及关联表中的任意字段
  - 自动创建 MySQL 外键约束

### 数据管理

- **动态 CRUD**：每张数据表支持增删改查
- **搜索**：基于可列表显示字段的模糊搜索
- **分页**：支持 AJAX 分页
- **关联选择**：`relation` 类型字段渲染为下拉框，选项来自关联表

### 用户与权限

- **根用户**：`is_root=true` 拥有全部权限，无需配置
- **用户组**：创建用户组，配置组内权限
- **用户**：创建用户并归属用户组，支持启用/禁用
- **权限配置**：按表粒度配置增删改查
  - `_forms` - 表单管理（表单、字段、分组、关联）
  - `_users` - 用户管理（用户组、用户、权限）
  - **数据表名**（如 `sample_articles`）- 对应表的数据操作权限

## 技术栈

- **后端**：Laravel 10、PHP 8.1+
- **前端**：Bootstrap 5、jQuery、仿 Element UI 风格
- **数据库**：MySQL 5.7+

## 环境要求

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Laravel 10.x

## 安装

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

访问 http://localhost:8000 ，默认账号：`admin` / `admin123`

## 目录结构

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php        # 登录/登出
│   │   ├── DashboardController.php   # 首页
│   │   ├── FormController.php        # 表单 CRUD
│   │   ├── FormFieldController.php   # 字段 CRUD
│   │   ├── FormGroupController.php   # 表单分组
│   │   ├── FormRelationController.php # 关联表单/外键
│   │   ├── TableDataController.php   # 动态数据 CRUD
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
├── migrations/
│   ├── create_user_groups_table
│   ├── create_users_table
│   ├── create_forms_table
│   ├── create_form_fields_table
│   ├── create_group_permissions_table
│   ├── create_sample_table
│   ├── create_form_groups_table      # 表单分组
│   └── create_form_relations_table   # 关联表单/外键
└── seeders/
    └── DatabaseSeeder.php
resources/views/
├── layouts/app.blade.php             # 主布局（顶栏、侧边栏、主内容）
├── login.blade.php
├── dashboard.blade.php
├── forms/                            # 表单管理
├── form-groups/                      # 表单分组
├── form-fields/                      # 字段配置
├── table-data/                       # 数据增删改查
├── users/                            # 用户管理
├── user-groups/                      # 用户组
├── permissions/                      # 权限配置
└── no-permission.blade.php
public/
└── css/app.css                       # 全局样式（Element UI 风格）
routes/
└── web.php
```

## 权限说明

| 表名 | 说明 |
|------|------|
| `_forms` | 表单管理（表单、字段、分组、关联） |
| `_users` | 用户管理（用户组、用户、权限） |
| 数据表名 | 该表数据的增删改查 |

根用户（`is_root=true`）拥有所有权限，无需配置。

## 界面说明

- **导航栏**：首页、表单管理（表单分组 / 表单列表）、用户管理（用户组 / 用户列表）
- **侧边栏**：按分组展示表单，点击分组可展开/收起；支持折叠为仅显示首字
- **主内容区**：卡片式布局，表格、表单、弹窗

## License

MIT
