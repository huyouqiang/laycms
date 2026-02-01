#### laycms

<img src="/public/logo_1.png">

#### 基本介绍
> 基于`layui2+php8+mysql8`搭建的通用数据库管理系统，通过`mysql系统表`查出所有`表单信息`并生成`菜单`和`数据表格`，查出表单所有`字段信息`并生成`form控件`，实现`增删改查`

#### 安装环境
> nginx/apache/php8/mysql8

#### 数据类型
> 写好字段的`注释`，自动生成`表单控件`，注释`基本格式`：字段中文[form控件类型|其他属性]

<table>
<tr>
  <th>序号</th>
  <th>类型</th>
  <th>长度</th>
  <th>注释</th>
</tr>
<tr>
  <td>1</td>
  <td>int</td>
  <td>不限</td>
  <td>id[input]</td>
</tr>
<tr>
  <td>2</td>
  <td>datetime</td>
  <td>0</td>
  <td>时间[date|Y-m-d H:i:s]</td>
</tr>
<tr>
  <td>3</td>
  <td>tinyint</td>
  <td>不限</td>
  <td>性别[radio|1=男&2=女&3=保密]</td>
</tr>
<tr>
  <td>4</td>
  <td>varchar</td>
  <td>10</td>
  <td>爱好[checkbox|1=看书&2=旅游&3=音乐]</td>
</tr>
<tr>
  <td>5</td>
  <td>varchar</td>
  <td>255</td>
  <td>头像[file]</td>
</tr>
<tr>
  <td>6</td>
  <td>json</td>
  <td>0</td>
  <td>配置[json]</td>
</tr>
<tr>
  <td>7</td>
  <td>text</td>
  <td>0</td>
  <td>富文本[editor]</td>
</tr>
<tr>
  <td>8</td>
  <td>float</td>
  <td>不限</td>
  <td>浮点型[float]</td>
</tr>
<tr>
  <td>9</td>
  <td>tinyint</td>
  <td>不限</td>
  <td>下拉框[select|1=男&2=女]</td>
</tr>
</table>

#### 模型管理

```bash
# group:分组，name_en:表名，name_ch:表名别名，显示主表，子表自动识别
[
  {
    "group": "学生分组1",
    "model": [
      {
        "name_en": "usr_student",
        "name_ch": "学生列表1"
      }
    ]
  },
  {
    "group": "学生分组2",
    "model": [
      {
        "name_en": "usr_school",
        "name_ch": "学校列表1"
      }
    ]
  }
]
```

#### 用户管理

```bash
# 根用户操作其他用户信息
# models:数据表，pages:页面
[
  {
    "userName": "adminer",
    "passWord": "123456",
    "models": "*",
    "pages": "*"
  },
  {
    "userName": "guest",
    "passWord": "123456",
    "models": "usr_student,usr_school",
    "pages": "/model/data,/backup"
  }
]
```

<table class="layui-table">
  <colgroup>
    <col width="150">
    <col width="150">
    <col>
  </colgroup>
  <thead>
  <tr>
    <th>账号</th>
    <th>密码</th>
    <th>模型</th>
    <th>页面</th>
  </tr>
  </thead>
  <tbody>
              <tr>
    <td>adminer</td>
    <td>123456</td>
    <td>*</td>
    <td>*</td>
  </tr>
              <tr>
    <td>guest</td>
    <td>123456</td>
    <td>usr_student,usr_school</td>
    <td>/model/data,/backup</td>
  </tr>

  </tbody>
</table>

#### 数据库

```bash
# 数据库配置文件
/app/config/Database.php

# 示例
public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '20262026',
        'database'     => 'laycms',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'port'         => 3306,
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];
```

#### 文件权限

```bash
# 模型数据、用户管理、系统设置数据
chmod 777 /writable/cache
```

#### 更新日志

<table class="layui-table">
  <thead>
  <tr>
    <th>日期</th>
    <th>功能</th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td>2026-02-01</td>
    <td>实现基本功能，包括：模型管理、用户管理、数据管理、系统设置</td>
  </tr>
  </tbody>
</table>
