# laycms
基于layui2+php8+mysql8搭建的通用数据库管理系统，通过mysql系统表查出所有表单信息并生成菜单和数据表格，查出表单所有字段信息并生成form控件，实现增删改查

# 数据类型
> 写好字段的`注释`，自动生成`表单控件`，注释`基本格式`：字段中文[form控件类型|其他属性]

<table>
<tr>
  <th>类型</th>
  <th>长度</th>
  <th>注释</th>
</tr>
<tr>
  <td>int</td>
  <td>不限</td>
  <td>id[input]</td>
</tr>
<tr>
  <td>datetime</td>
  <td>0</td>
  <td>时间[date|Y-m-d H:i:s]</td>
</tr>
<tr>
  <td>tinyint</td>
  <td>不限</td>
  <td>性别[radio|1=男&2=女&3=保密]</td>
</tr>
<tr>
  <td>varchar</td>
  <td>10</td>
  <td>爱好[checkbox|1=看书&2=旅游&3=音乐]</td>
</tr>
<tr>
  <td>varchar</td>
  <td>255</td>
  <td>头像[file]</td>
</tr>
<tr>
  <td>json</td>
  <td>0</td>
  <td>配置[json]</td>
</tr>
<tr>
  <td>text</td>
  <td>0</td>
  <td>富文本[editor]</td>
</tr>
<tr>
  <td>float</td>
  <td>不限</td>
  <td>浮点型[float]</td>
</tr>
<tr>
  <td>tinyint</td>
  <td>不限</td>
  <td>下拉框[select|1=男&2=女]</td>
</tr>
</table>
