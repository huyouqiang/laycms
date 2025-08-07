<style>
  .demo-login-container{width: 320px; margin: 21px auto 0;}
  .demo-login-other .layui-icon{position: relative; display: inline-block; margin: 0 2px; top: 2px; font-size: 26px;}
  body {
    background-image:  url(/public/bg_3.png);
    * 背景图垂直、水平均居中 */
    background-position: center center;
    /* 背景图不平铺 */
    background-repeat: no-repeat;
    /* 当内容高度大于图片高度时，背景图像的位置相对于viewport固定 */
    background-attachment: fixed;
    /* 让背景图基于容器大小伸缩 */
    background-size: 100% 100%;
    opacity: 0.95; /* 透明度设置为50% */
    filter: alpha(Opacity=95);
    -moz-opacity: 0.95;
    opacity: 0.95;
    -khtml-opacity: 0.95;
  }

</style>
<div class="layui-header layui-bg-cyan">
  <a href="/"><div class="layui-logo layui-hide-xs layui-bg-black" style="background-image: url(/public/logo_1.png);background-size: 100% 100%;"></div></a>
  <!-- 头部区域（可配合layui 已有的水平导航） <a href="/" style="color: #ffffff;"><img src="/public/logo.png" style="width: 80px;height: 40px;"></a>-->
  <ul class="layui-nav layui-layout-left">
    <!-- 移动端显示 -->
    <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
      <i class="layui-icon layui-icon-spread-left"></i>
    </li>
    <li class="layui-nav-item layui-hide-xs <?php if (strpos($_SERVER['REQUEST_URI'], '/model/settings') !== false): ?>layui-this<?php endif ?>"><a href="/model/settings"><i class="layui-icon layui-icon-table"></i> 模型管理</a></li>
    <li class="layui-nav-item layui-hide-xs"><a href="/users"><i class="layui-icon layui-icon-user"></i> 用户管理</a></li>
    <li class="layui-nav-item layui-hide-xs"><a href="/users"><i class="layui-icon layui-icon-export"></i> 数据备份</a></li>
    <li class="layui-nav-item layui-hide-xs"><a href="/users"><i class="layui-icon layui-icon-set"></i> 系统设置</a></li>
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">用户管理</a></li>-->
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">备份还原</a></li>-->
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">系统设置</a></li>-->
  </ul>
  <ul class="layui-nav layui-layout-right">
    <li class="layui-nav-item layui-hide layui-show-sm-inline-block">
      <a href="javascript:;">
        <img src="/public/logo.png" class="layui-nav-img">
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] != ''): ?><?php echo $_SESSION['login']['userName']; ?><?php else: ?>请登录<?php endif ?>
      </a>
      <dl class="layui-nav-child">
        <dd><a href="/login"><i class="layui-icon layui-icon-logout"></i> &nbsp;退 &nbsp;出</a></dd>
      </dl>
    </li>
  </ul>
</div>
