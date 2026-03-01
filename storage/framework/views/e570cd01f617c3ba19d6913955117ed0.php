<?php $__env->startSection('title', '首页'); ?>

<?php $__env->startSection('content'); ?>
<div class="alert alert-info">欢迎使用 LayCMS 动态表单管理系统</div>
<div class="row g-3">
    <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><?php echo e($form->name); ?></div>
            <div class="card-body">
                <p class="card-text"><?php echo e($form->description ?? '暂无描述'); ?></p>
                <a href="<?php echo e(route('table-data.index', $form->table_name)); ?>" class="btn btn-sm btn-primary">管理数据</a>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read')): ?>
                <a href="<?php echo e(route('form-fields.index', $form)); ?>" class="btn btn-sm btn-outline-primary">字段配置</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/dashboard.blade.php ENDPATH**/ ?>