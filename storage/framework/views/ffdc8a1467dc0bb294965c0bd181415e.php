<?php $__env->startSection('title', '表单管理'); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span>表单列表</span>
        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'create')): ?>
        <a href="<?php echo e(route('forms.create')); ?>" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-add-1"></i> 新建表单</a>
        <?php endif; ?>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table" lay-skin="line">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>表单分组</th>
                    <th>表单名称</th>
                    <th>数据表</th>
                    <th>字段数</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($f->id); ?></td>
                    <td><?php echo e($f->formGroup?->name ?? '-'); ?></td>
                    <td><?php echo e($f->name); ?></td>
                    <td><?php echo e($f->table_name); ?></td>
                    <td><?php echo e($f->fields_count); ?></td>
                    <td>
                        <a href="<?php echo e(route('table-data.index', $f->table_name)); ?>" class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon layui-icon-table"></i> 数据</a>
                        <a href="<?php echo e(route('form-fields.index', $f)); ?>" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-list"></i> 字段</a>
                        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
                        <a href="<?php echo e(route('forms.edit', $f)); ?>" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-edit"></i> 编辑</a>
                        <?php endif; ?>
                        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'delete')): ?>
                        <form action="<?php echo e(route('forms.destroy', $f)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
layui.use(['element'], function(){ layui.element.render('table'); });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/forms/index.blade.php ENDPATH**/ ?>