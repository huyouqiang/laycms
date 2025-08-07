<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>laycms - 首页数据</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="//unpkg.com/layui@2.11.5/dist/css/layui.css" rel="stylesheet">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <?= $this->include('public/header') ?>
  <?= $this->include('public/left') ?>

  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
      <div class="layui-card layui-panel">
        <div class="layui-card-header">
          <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a><cite>网站信息</cite></a>
          </span>
        </div>
        <div class="layui-card-body">


          <table class="layui-table" id="demo">
            <tr>
              <td>网站名称</td>
              <td>laycms</td></tr>
            <tr>
              <td>平台版本</td>
              <td>laycms v1.0.0</td></tr>
            <tr>
              <td>脚本语言</td>
              <td><?php echo 'php '.PHP_VERSION; ?></td></tr>
            <tr>
              <td>数据库</td>
              <td><?= 'mysql '.$sqlVersion['sqlVersion'] ?></td></tr>
          </table>


        </div>
      </div>
      <br><br>
    </div>
  </div>
  <div class="layui-footer">
    <!-- 底部固定区域 -->
    底部固定区域
  </div>
</div>

<script src="//unpkg.com/layui@2.11.5/dist/layui.js"></script>
<script>
  //JS
  layui.use(['element', 'layer', 'util', 'table'], function(){
    var element = layui.element;
    var layer = layui.layer;
    var util = layui.util;
    var table = layui.table;
    var $ = layui.$;



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
      }
    });
  });
</script>
</body>
</html>
