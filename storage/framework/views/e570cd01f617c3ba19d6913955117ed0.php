<?php $__env->startSection('title', '首页'); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card">
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote layui-quote-nm layui-bg-cms">欢迎使用 LayCMS 动态表单管理系统</blockquote>
    </div>
</div>
<div class="layui-row layui-col-space15">
    <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="layui-col-md4">
        <div class="layui-card">
            <div class="layui-card-header"><?php echo e($form->name); ?></div>
            <div class="layui-card-body">
                <p style="color:#666;margin-bottom:15px;"><?php echo e($form->description ?? '暂无描述'); ?></p>
                <a href="<?php echo e(route('table-data.index', $form->table_name)); ?>" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-table"></i> 管理数据</a>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read')): ?>
                <a href="<?php echo e(route('form-fields.index', $form)); ?>" class="layui-btn layui-btn-sm layui-btn-primary"><i class="layui-icon layui-icon-set"></i> 字段配置</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/dashboard.blade.php ENDPATH**/ ?>