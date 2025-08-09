<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>laycms - 模型管理</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/public/layui/css/layui.css" rel="stylesheet">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <?= $this->include('public/header') ?>
  <?= $this->include('public/left') ?>

  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
      <div class="layui-card layui-panel" style="height: calc(85vh)">
        <div class="layui-card-header">
          <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a><cite>模型管理</cite></a>
          </span>
        </div>
        <div class="layui-card-body">
          <div class="layui-btn-group" style="margin-bottom: 10px;">
            <a type="button" class="layui-btn layui-btn-primary layui-btn-sm" href="#" lay-header-event="modelJson">
              <i class="layui-icon layui-icon-addition"></i>模型配置json
            </a>
          </div>
          <div class="layui-collapse">
            <?php foreach ($menus as $key => $value): ?>
            <div class="layui-colla-item">
              <div class="layui-colla-title">
                <?= $value['group'] ?>
              </div>
              <div class="layui-colla-content layui-show">
                <table class="layui-table">
                  <colgroup>
                    <col width="150">
                    <col width="150">
                    <col>
                  </colgroup>
                  <thead>
                  <tr>
                    <th>模型标识</th>
                    <th>模型名称</th>
                    <th>模型排序</th>
                    <th>字段列表</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($value['model'] as $key1 => $value1): ?>
                  <tr>
                    <td><?= $value1['name_en'] ?></td>
                    <td><?= $value1['name_ch'] ?></td>
                    <td><?= $key1+1 ?></td>
                    <td>
                      <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-sm">
                          <i class="layui-icon layui-icon-table"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach ?>

                  </tbody>
                </table>
              </div>
            </div>
            <?php endforeach ?>
        </div>
      </div>
      <br><br>
    </div>
  </div>
    <?= $this->include('public/footer') ?>
</div>

<script src="//unpkg.com/layui@2.11.5/dist/layui.js"></script>
<script>
  //JS
  layui.use(['element', 'layer', 'util', 'table', 'form'], function(){
    var element = layui.element;
    var layer = layui.layer;
    var util = layui.util;
    var $ = layui.$;
    var table = layui.table;
    var form = layui.form;

    //头部事件
    util.event('lay-header-event', {
      menuLeft: function(othis){ // 左侧菜单事件
        layer.msg('展开左侧菜单的操作', {icon: 0});
      },
      menuRight: function(){  // 右侧菜单事件
        layer.open({
          type: 1,
          title: '更多',
          content: '<div style="padding: 15px;">处理右侧面板的操作</div>',
          area: ['260px', '100%'],
          offset: 'rt', // 右上角
          anim: 'slideLeft', // 从右侧抽屉滑出
          shadeClose: true,
          scrollbar: false
        });
      },
      modelJson: function(obj){ // 左侧菜单事件

        layer.open({
          type: 1, // page 层类型
          area: ['600px', '600px'],
          title: '模型配置json',
          shade: 0.6, // 遮罩透明度
          shadeClose: true, // 点击遮罩区域，关闭弹层
          maxmin: false, // 允许全屏最小化
          anim: 0, // 0-6 的动画形式，-1 不开启
          content: '<div style="padding: 32px;"><form class="layui-form" lay-filter="demo-val-filter"><div class="layui-form-item"><textarea placeholder="请输入内容" class="layui-textarea" rows="20" name="modelJson"></textarea></div><button class="layui-btn layui-btn-primary layui-btn-fluid" lay-submit="" lay-filter="demo-submit">保存</button></form></div>'
        });

        form.render();
        form.val('demo-val-filter', {modelJson:'<?= json_encode($menus) ?>'});

        // 提交事件
        form.on('submit(demo-submit)', function(formData){
          console.log(formData);
          var field = formData.field; // 获取表单全部字段值
          var elem = formData.elem; // 获取当前触发事件的元素 DOM 对象，一般为 button 标签
          var elemForm = formData.form; // 获取当前表单域的 form 元素对象，若容器为 form 标签才会返回。
          // 显示填写结果，仅作演示用
          // layer.alert(JSON.stringify(field), {
          //   title: '当前填写的字段值'
          // });

          $.ajax({
            type:"POST",                      //请求类型
            url:"/model/modeljson",           //URL
            // dataType: "json",
            data:field,   //传递的参数
            success:function(res){          //data就是返回的json类型的数据

              if(res.code=='1'){
                layer.msg(res.msg);
              }
              else{
                layer.msg(res.msg);
              }

            },
            error:function(res){

            }
          });
          // …
          return false; // 阻止默认 form 跳转
        });

      }
    });
  });
</script>
</body>
</html>
