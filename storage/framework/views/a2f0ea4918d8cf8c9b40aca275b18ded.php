<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'LayCMS'); ?> - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/css/layui.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<style>
.layui-nav{
    background-color: #001529;
}
.layui-laypage .layui-laypage-curr .layui-laypage-em{
    background-color: #001529;
}
.layui-form-radio:hover>*, .layui-form-radioed, .layui-form-radioed>i{
    color: #001529;
}
.layui-bg-green{
    background-color: #001529 !important;
}
.layui-nav-child{
    background-color: #001529;
    border-color: #001529;
}
.layui-layer-btn .layui-layer-btn0{
    background-color: #001529;
}
.layui-input:focus, .layui-textarea:focus{
    border-color: #001529 !important;
}
.layui-form-select dl dd.layui-this{
    color: #001529 !important;
}
.layui-form-onswitch{
    background-color: #001529;
    border-color: #001529;
}
</style>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header layui-bg-black">
            <div class="layui-logo" style="width:210px;left:0;text-align:center;">
                <a href="<?php echo e(route('dashboard')); ?>" style="color:#fff;font-size:18px;font-weight:600;"><i class="layui-icon layui-icon-website"></i> LayCMS</a>
            </div>
            <ul class="layui-nav layui-layout-left" style="left:210px;">
                <li class="layui-nav-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="layui-icon layui-icon-home"></i> 首页</a></li>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read')): ?>
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-template"></i> 表单管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?php echo e(route('form-groups.index')); ?>"><i class="layui-icon layui-icon-read"></i> 表单分组</a></dd>
                        <dd><a href="<?php echo e(route('forms.index')); ?>"><i class="layui-icon layui-icon-file"></i> 表单列表</a></dd>
                    </dl>
                </li>
                <?php endif; ?>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_users', 'read')): ?>
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-user"></i> 用户管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?php echo e(route('user-groups.index')); ?>">用户组</a></dd>
                        <dd><a href="<?php echo e(route('users.index')); ?>">用户列表</a></dd>
                    </dl>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-username"></i> <?php echo e($cms_user->nickname ?? $cms_user->username); ?></a>
                    <dl class="layui-nav-child">
                        <dd style="padding:10px 20px;color:#999;">
                            <?php if($cms_user->is_root): ?> 角色：超级管理员
                            <?php elseif($cms_user->userGroup): ?> 角色：<?php echo e($cms_user->userGroup->name); ?>

                            <?php else: ?> 角色：-
                            <?php endif; ?>
                        </dd>
                        <dd><a href="<?php echo e(route('logout')); ?>"><i class="layui-icon layui-icon-logout"></i> 退出登录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
        <?php $currentTable = request()->route('tableName') ?? ''; ?>
        <div class="layui-side layui-bg-black" style="top:60px;width:210px;">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree" lay-filter="sidebar" style="margin-top:0;">
                    <?php $__currentLoopData = $menu_form_groups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;"><i class="layui-icon layui-icon-read"></i> <?php echo e($grp->name); ?></a>
                        <dl class="layui-nav-child">
                            <?php $__currentLoopData = $grp->forms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($cms_user->is_root || $cms_user->canAccessTable($f->table_name, 'read')): ?>
                            <dd><a href="<?php echo e(route('table-data.index', $f->table_name)); ?>" class="<?php echo e((request()->route('tableName') ?? '') === $f->table_name ? 'layui-this' : ''); ?>"><i class="layui-icon layui-icon-table"></i> <?php echo e($f->name); ?></a></dd>
                            <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </dl>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <div class="layui-body" style="top:60px;left:210px;bottom:0;">
            <div style="padding:20px;">
                <?php if(session('success')): ?>
                <div class="layui-elem-quote layui-quote-nm layui-bg-green" style="margin-bottom:15px;"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                <div class="layui-elem-quote layui-quote-nm layui-bg-red" style="margin-bottom:15px;"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($e); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('modals'); ?>
    <script src="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/layui.js"></script>
    <script>
    layui.config({ base: '' }).use(['element', 'layer'], function(){
        var element = layui.element;
        window.layui = layui;
        window.$ = window.jQuery = layui.$;
        element.render('nav');
    });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /opt/homebrew/var/www/laycms/resources/views/layouts/app.blade.php ENDPATH**/ ?>