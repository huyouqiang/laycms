<?php $__env->startSection('title', ($row ? '编辑' : '新增') . ' - ' . $form->name); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header"><?php echo e($row ? '编辑' : '新增'); ?> <?php echo e($form->name); ?></div>
    <div class="card-body">
        <form action="<?php echo e($row ? route('table-data.update', [$tableName, $row->id]) : route('table-data.store', $tableName)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php if($row): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
            <?php $__currentLoopData = $form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-3">
                <label class="form-label<?php echo e($field->is_required ? ' required' : ''); ?>"><?php echo e($field->label); ?></label>
                <?php echo $__env->make('table-data.field-control', ['field' => $field, 'value' => $row?->{$field->field_name} ?? old($field->field_name) ?? ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="<?php echo e(route('table-data.index', $tableName)); ?>" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/table-data/form.blade.php ENDPATH**/ ?>