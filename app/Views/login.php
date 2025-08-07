<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>laycms - 用户登录</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="//unpkg.com/layui@2.11.5/dist/css/layui.css" rel="stylesheet">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <?= $this->include('public/header') ?>
  <div class="layui-body" style="margin-top: calc(30vh - 100px);left: 35%;">
    <style>
      .demo-login-container{width: 320px; margin: 21px auto 0;}
      .demo-login-other .layui-icon{position: relative; display: inline-block; margin: 0 2px; top: 2px; font-size: 26px;}
      body {
        background-image:  url(http://hutc.top/public/bg.jpeg);
        * 背景图垂直、水平均居中 */
        background-position: center center;
        /* 背景图不平铺 */
        background-repeat: no-repeat;
        /* 当内容高度大于图片高度时，背景图像的位置相对于viewport固定 */
        background-attachment: fixed;
        /* 让背景图基于容器大小伸缩 */
        background-size: cover;
        opacity: 0.95; /* 透明度设置为50% */
        filter: alpha(Opacity=95);
        -moz-opacity: 0.95;
        opacity: 0.95;
        -khtml-opacity: 0.95;
      }

    </style>
    <div class="layui-panel" style="width: 400px;">
      <div style="padding: 32px;">

        <form class="layui-form">
          <div class="demo-login-container">
            <div class="layui-form-item">
              <div class="layui-input-wrap">
                <div class="layui-input-prefix">
                  <i class="layui-icon layui-icon-username"></i>
                </div>
                <input type="text" name="userName" value="" lay-verify="required" placeholder="用户名" lay-reqtext="请填写用户名" autocomplete="off" class="layui-input" lay-affix="clear">
              </div>
            </div>
            <div class="layui-form-item">
              <div class="layui-input-wrap">
                <div class="layui-input-prefix">
                  <i class="layui-icon layui-icon-password"></i>
                </div>
                <input type="password" name="passWord" value="" lay-verify="required" placeholder="密   码" lay-reqtext="请填写密码" autocomplete="off" class="layui-input" lay-affix="eye">
              </div>
            </div>
<!--            <div class="layui-form-item">-->
<!--              <div class="layui-row">-->
<!--                <div class="layui-col-xs7">-->
<!--                  <div class="layui-input-wrap">-->
<!--                    <div class="layui-input-prefix">-->
<!--                      <i class="layui-icon layui-icon-vercode"></i>-->
<!--                    </div>-->
<!--                    <input type="text" name="captcha" value="" lay-verify="required" placeholder="验证码" lay-reqtext="请填写验证码" autocomplete="off" class="layui-input" lay-affix="clear">-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="layui-col-xs5">-->
<!--                  <div style="margin-left: 10px;">-->
<!--                    <img src="https://www.oschina.net/action/user/captcha" onclick="this.src='https://www.oschina.net/action/user/captcha?t='+ new Date().getTime();">-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
            <div class="layui-form-item">
              <input type="checkbox" name="remember" lay-skin="primary" title="记住密码">
              <a href="#forget" style="float: right; margin-top: 7px;">忘记密码？</a>
            </div>
            <div class="layui-form-item">
              <button type="button" class="layui-btn layui-btn-fluid" lay-submit lay-filter="demo-login">登录</button>
            </div>

          </div>
        </form>


      </div>
    </div>

  </div>
  <div class="layui-footer" style="left: 0px;">
    <!-- 底部固定区域 -->
    底部固定区域
  </div>
</div>

<script src="//unpkg.com/layui@2.11.5/dist/layui.js"></script>
<script>
  layui.use(function(){
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.$;
    // 提交事件
    form.on('submit(demo-login)', function(data){
      var field = data.field; // 获取表单字段值
      // 显示填写结果，仅作演示用
      // layer.alert(JSON.stringify(field), {
      //   title: '当前填写的字段值'
      // });
      // 此处可执行 Ajax 等操作
      $.ajax({
        type:"GET",                      //请求类型
        url:"/login?userName=" + field.userName + '&passWord=' + field.passWord,           //URL
        dataType: "json",
        data:'',   //传递的参数
        success:function(res){          //data就是返回的json类型的数据
          if(res.code=='1'){
            layer.msg(res.msg);
            window.location.href = "/";
          }
          else{
            layer.msg(res.msg);
          }
        }
      });
      // …
      return false; // 阻止默认 form 跳转
    });
  });
</script>
</body>
</html>
