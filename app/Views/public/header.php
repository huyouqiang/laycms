<div class="layui-header">
  <div class="layui-logo layui-hide-xs layui-bg-black"><a href="/" style="color: #ffffff;">laycms</a></div>
  <!-- 头部区域（可配合layui 已有的水平导航） -->
  <ul class="layui-nav layui-layout-left">
    <!-- 移动端显示 -->
    <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
      <i class="layui-icon layui-icon-spread-left"></i>
    </li>
    <li class="layui-nav-item layui-hide-xs"><a href="/model/settings">模型管理</a></li>
    <li class="layui-nav-item layui-hide-xs"><a href="/users">用户管理</a></li>
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">用户管理</a></li>-->
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">备份还原</a></li>-->
    <!--      <li class="layui-nav-item layui-hide-xs"><a href="javascript:;">系统设置</a></li>-->
  </ul>
  <ul class="layui-nav layui-layout-right">
    <li class="layui-nav-item layui-hide layui-show-sm-inline-block">
      <a href="javascript:;">
        <img src="/public/logo.jpeg" class="layui-nav-img">
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] != ''): ?><?php echo $_SESSION['login']['userName']; ?><?php else: ?>请登录<?php endif ?>
      </a>
      <dl class="layui-nav-child">
        <dd><a href="/login">退出</a></dd>
      </dl>
    </li>
  </ul>
</div>
