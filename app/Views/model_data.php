<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>laycms - 数据表格</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="//unpkg.com/layui@2.11.5/dist/css/layui.css" rel="stylesheet">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo layui-hide-xs layui-bg-black"><a href="/" style="color: #ffffff;">laycms</a></div>
    <!-- 头部区域（可配合layui 已有的水平导航） -->
    <ul class="layui-nav layui-layout-left">
      <!-- 移动端显示 -->
      <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
        <i class="layui-icon layui-icon-spread-left"></i>
      </li>
      <li class="layui-nav-item layui-hide-xs"><a href="/model/settings">模型管理</a></li>
<!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">用户管理</a></li>-->
<!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">备份还原</a></li>-->
<!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">系统设置</a></li>-->
    </ul>
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item layui-hide layui-show-sm-inline-block">
        <a href="javascript:;">
          <img src="//unpkg.com/outeres@0.0.10/img/layui/icon-v2.png" class="layui-nav-img">
          tester
        </a>
        <dl class="layui-nav-child">
          <dd><a href="javascript:;">Your Profile</a></dd>
          <dd><a href="javascript:;">Settings</a></dd>
          <dd><a href="javascript:;">Sign out</a></dd>
        </dl>
      </li>

    </ul>
  </div>
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree" lay-filter="test">
        <?php foreach ($menus as $key => $value): ?>
        <li class="layui-nav-item layui-nav-itemed">
          <a class="" href="javascript:;"><?= $value['group'] ?></a>
          <dl class="layui-nav-child">
            <?php foreach ($value['model'] as $key1 => $value1): ?>
            <dd class="<?php if (strpos(uri_string(), $value1['name_en']) !== false): ?>layui-this<?php endif ?>"><a href="/model/data/<?= $value1['name_en'] ?>"><?= $value1['name_ch'] ?></a></dd>
            <?php endforeach ?>
          </dl>
        </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
  <div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
      <div class="layui-card layui-panel">
        <div class="layui-card-header">
          <span class="layui-breadcrumb">
            <a href="/">首页</a>
            <a href="">模型</a>
            <a><cite><?= $modelName ?></cite></a>
          </span>
        </div>
        <div class="layui-card-body">
          <table class="layui-hide" id="test"></table>
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

<script type="text/html" id="toolDemo">
  <div class="layui-btn-group">
    <button type="button" class="layui-btn layui-btn-sm" lay-event="edit">
      <i class="layui-icon layui-icon-edit"></i>
    </button>
    <button type="button" class="layui-btn layui-btn-sm" lay-event="delete">
      <i class="layui-icon layui-icon-delete"></i>
    </button>
  </div>
</script>

<script src="//unpkg.com/layui@2.11.5/dist/layui.js"></script>
<script>

  layui.use(['element', 'layer', 'util', 'form', 'table', 'upload', 'tabs'], function(){
    var element = layui.element;
    var layer = layui.layer;
    var util = layui.util;
    var form = layui.form;
    var table = layui.table;
    var upload = layui.upload;
    var tabs = layui.tabs;
    var $ = layui.$;


    // 创建渲染实例
    table.render({
      elem: '#test',
      url: '/<?= uri_string() ?>', // 此处为静态模拟数据，实际使用时需换成真实接口
      height: 'full-200', // 最大高度减去其他容器已占有的高度差
      cellMinWidth: 120,
      page: true,
      limit: 50,
      cols: [<?= $fieldJson ?>],
    });

    // 触发单元格工具事件
    table.on('tool(test)', function(obj){
      var data = obj.data; // 获得当前行数据
      var rowId = data.id;
      if(obj.event === 'edit'){

        $.ajax({
          type:"get",                      //请求类型
          url:"/model/rowform/<?= $modelName ?>/" + data.id,           //URL
          // dataType: "json",
          // data:formData.field,   //传递的参数
          success:function(res){
            // layer.msg(res.msg);


            layer.open({
              title: '编辑',
              type: 1,
              area: ['100%','100%'],
              content: '<div id="demoTabs2"></div>'
            });

            console.log(res);

            // 方法渲染
            tabs.render({
              elem: '#demoTabs2',
              header: res.header,
              body: res.body,
            });

            form.render();

            tabs.on('afterChange(demoTabs2)', function(data) {
              var index = data.index;
              var tabName = $(this).html();
              tabName = tabName.substring(0, tabName.indexOf('&'));
              // console.log(tabName);
              // layer.msg(tabName);
              if (index > 0) {

                $.ajax({
                  type:"GET",                      //请求类型
                  url:'/model/data/' + tabName + '?t=child',           //URL
                  dataType: "json",
                  data:'',   //传递的参数
                  success:function(res){          //data就是返回的json类型的数据

                    table.render({
                      elem: '#' + tabName,
                      url: '/model/data/' + tabName + '?t=child&pid=' + rowId, // 此处为静态模拟数据，实际使用时需换成真实接口
                      height: 'full-100', // 最大高度减去其他容器已占有的高度差
                      cellMinWidth: 120,
                      page: true,
                      limit: 50,
                      cols: [JSON.parse(res.fieldJson)],
                    });

                    table.on('tool(' + tabName + ')', function(obj) {

                      if (obj.event === 'edit') {

                        // alert('huhu');



                        var childData = obj.data; // 获得当前行数据
                        $.ajax({
                          type:"get",                      //请求类型
                          url:"/model/rowform/" + tabName + "/" + childData.id + "?t=child",           //URL
                          // dataType: "json",
                          // data:formData.field,   //传递的参数
                          success:function(res){

                            // alert(res);

                            layer.open({
                              title: '编辑',
                              type: 1,
                              area: ['100%','100%'],
                              content: '<div id="demoTabs3"></div>'
                            });

                            console.log(res);

                            // 方法渲染
                            tabs.render({
                              elem: '#demoTabs3',
                              header: res.header,
                              body: res.body,
                            });

                            form.render();

                            // 单图片上传
                            upload.render({
                              elem: '.uploadFile',
                              url: '/model/uploadfile', // 实际使用时改成您自己的上传接口即可。
                              before: function(obj){
                                layer.msg('上传中...');
                              },
                              done: function(res){
                                if(res.code == '1'){
                                  // $(this).next().html(res.msg);
                                  console.log(this.item);
                                  var uploadFieldName = $(this.item).attr('uploadFieldName');
                                  $("#" + uploadFieldName + '_btn').text(res.msg);
                                  $("#" + uploadFieldName + '_btn').attr('href', res.msg);
                                  $("#" + uploadFieldName).val(res.msg);
                                  // layer.msg($("#" + uploadFieldName).val());

                                }
                                console.log(obj);
                              }
                            });

                          }
                        });
                      }
                      else if(obj.event === 'delete') {
                        var childData = obj.data; // 获得当前行数据
                        layer.confirm('真的删除行 [id: '+ childData.id +'] 么', function(index){
                          $.ajax({
                            type:"GET",                      //请求类型
                            url:"/model/rowdel/" + tabName + "/" + childData.id,           //URL
                            dataType: "json",
                            data:'',   //传递的参数
                            success:function(res){          //data就是返回的json类型的数据
                              if(res.code=='1'){
                                layer.msg(res.msg);
                              }
                              else{
                                layer.msg(res.msg);
                              }
                            }
                          });
                          obj.del(); // 删除对应行（tr）的DOM结构
                        });
                      }

                      // 提交事件
                      form.on('submit(child-submit)', function(formData){
                        console.log(formData);
                        alert('123');

                        var rowData = formData.field;
                        delete rowData.file;

                        // return false;

                        $('#' + tabName + ' input[type=checkbox]').each(function(key, value) {
                          var name = $(this).attr('checkboxName'); // 获取当前checkbox的name属性
                          var value = $(this).val(); // 获取当前checkbox的值
                          var isChecked = $(this).is(':checked'); // 检查是否被选中

                          if (!rowData[name]) {
                            rowData[name] = []; // 初始化数组
                          }
                          if (isChecked) {
                            rowData[name].push(value); // 如果被选中，添加到数组中
                          }

                        });

                        $.each(rowData, function(key, value) {

                          if ( key.indexOf('[') !== -1 ) {
                            // alert(key);
                            delete rowData[key];
                          }

                          if (Array.isArray(value)) {
                            rowData[key] = value.join(',');
                          }

                          // console.log(rowData);
                        });

                        // console.log(rowData);
                        //
                        // return false;

                        $.ajax({
                          type:"POST",                      //请求类型
                          url:"/model/rowupdate/" + tabName + "/" + formData.field.id,           //URL
                          // dataType: "json",
                          data:formData.field,   //传递的参数
                          success:function(res){
                            layer.msg(res.msg);
                            table.reload('test');
                          }
                        });

                      });

                    });

                  }
                });

              }
            });



            // 单图片上传
            upload.render({
              elem: '.uploadFile',
              url: '/model/uploadfile', // 实际使用时改成您自己的上传接口即可。
              before: function(obj){
                layer.msg('上传中...');
              },
              done: function(res){
                if(res.code == '1'){
                  // $(this).next().html(res.msg);
                  console.log(this.item);
                  var uploadFieldName = $(this.item).attr('uploadFieldName');
                  $("#" + uploadFieldName + '_btn').text(res.msg);
                  $("#" + uploadFieldName + '_btn').attr('href', res.msg);
                  $("#" + uploadFieldName).val(res.msg);
                  // layer.msg($("#" + uploadFieldName).val());

                }
                console.log(obj);
              }
            });

          }
        });

        // 提交事件
        form.on('submit(demo-submit)', function(formData){
          // console.log(formData);

          var rowData = formData.field;
          delete rowData.file;

          $('input[type=checkbox]').each(function(key, value) {
            var name = $(this).attr('checkboxName'); // 获取当前checkbox的name属性
            var value = $(this).val(); // 获取当前checkbox的值
            var isChecked = $(this).is(':checked'); // 检查是否被选中

            if (!rowData[name]) {
              rowData[name] = []; // 初始化数组
            }
            if (isChecked) {
              rowData[name].push(value); // 如果被选中，添加到数组中
            }

          });

          $.each(rowData, function(key, value) {

            if ( key.indexOf('[') !== -1 ) {
              // alert(key);
              delete rowData[key];
            }

            if (Array.isArray(value)) {
              rowData[key] = value.join(',');
            }

            // console.log(rowData);
          });

          // console.log(rowData);
          //
          // return false;

          $.ajax({
            type:"POST",                      //请求类型
            url:"/model/rowupdate/<?= $modelName ?>/" + formData.field.id,           //URL
            // dataType: "json",
            data:formData.field,   //传递的参数
            success:function(res){
              layer.msg(res.msg);
              table.reload('test');
            }
          });

        });
      }
      else if(obj.event === 'delete') {
        layer.confirm('真的删除行 [id: '+ data.id +'] 么', function(index){
          $.ajax({
            type:"GET",                      //请求类型
            url:"/model/rowdel/<?= $modelName ?>/" + data.id,           //URL
            dataType: "json",
            data:'',   //传递的参数
            success:function(res){          //data就是返回的json类型的数据
              if(res.code=='1'){
                layer.msg(res.msg);
              }
              else{
                layer.msg(res.msg);
              }
            }
          });
          obj.del(); // 删除对应行（tr）的DOM结构
        });
      }
    });

    

  });

</script>
