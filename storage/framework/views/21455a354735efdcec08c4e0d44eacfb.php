<?php $__env->startSection('title', '新建表单'); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card">
    <div class="layui-card-header">新建表单</div>
    <div class="layui-card-body">
        <form class="layui-form" action="<?php echo e(route('forms.store')); ?>" method="POST" style="max-width:600px;">
            <?php echo csrf_field(); ?>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单分组</label>
                <div class="layui-input-block">
                    <select name="form_group_id" required>
                        <option value="">请选择分组</option>
                        <?php $__currentLoopData = $formGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e(old('form_group_id') == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" value="<?php echo e(old('name')); ?>" placeholder="如：文章" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">数据表名</label>
                <div class="layui-input-block">
                    <input type="text" name="table_name" class="layui-input" value="<?php echo e(old('table_name')); ?>" placeholder="如：articles（小写字母数字下划线）" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" name="description" class="layui-input" value="<?php echo e(old('description')); ?>" placeholder="可选">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-add-circle"></i> 创建</button>
                    <a href="<?php echo e(route('forms.index')); ?>" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>layui.use('form', function(){ var form = layui.form; form.render('select'); });</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/forms/create.blade.php ENDPATH**/ ?>