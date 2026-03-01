<?php $__env->startSection('title', '新建表单'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">新建表单</div>
    <div class="card-body">
        <form action="<?php echo e(route('forms.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">表单分组</label>
                <select name="form_group_id" class="form-select" required>
                    <option value="">请选择分组</option>
                    <?php $__currentLoopData = $formGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($g->id); ?>" <?php echo e(old('form_group_id') == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">表单名称</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" placeholder="如：文章" required>
            </div>
            <div class="mb-3">
                <label class="form-label">数据表名</label>
                <input type="text" name="table_name" class="form-control" value="<?php echo e(old('table_name')); ?>" placeholder="如：articles（小写字母数字下划线）" required>
            </div>
            <div class="mb-3">
                <label class="form-label">描述</label>
                <input type="text" name="description" class="form-control" value="<?php echo e(old('description')); ?>" placeholder="可选">
            </div>
            <button type="submit" class="btn btn-primary">创建</button>
            <a href="<?php echo e(route('forms.index')); ?>" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/forms/create.blade.php ENDPATH**/ ?>