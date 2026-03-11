<?php $__env->startSection('title', '权限设置 - ' . $group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card">
    <div class="layui-card-header"><?php echo e($group->name); ?> - 权限设置</div>
    <div class="layui-card-body">
        <form class="layui-form" action="<?php echo e(route('permissions.update', $group)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="layui-form-item">
                <div class="layui-input-block" style="width:100%;">
                    <table class="layui-table">
                        <thead>
                            <tr><th>资源</th><th>新增</th><th>查看</th><th>编辑</th><th>删除</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tableName => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $p = $perms->get($tableName); ?>
                            <tr>
                                <td><?php echo e($label); ?></td>
                                <td><input type="checkbox" name="permissions[<?php echo e($tableName); ?>][create]" value="1" lay-skin="primary" <?php echo e($p && $p->can_create ? 'checked' : ''); ?>></td>
                                <td><input type="checkbox" name="permissions[<?php echo e($tableName); ?>][read]" value="1" lay-skin="primary" <?php echo e($p && $p->can_read ? 'checked' : ''); ?>></td>
                                <td><input type="checkbox" name="permissions[<?php echo e($tableName); ?>][update]" value="1" lay-skin="primary" <?php echo e($p && $p->can_update ? 'checked' : ''); ?>></td>
                                <td><input type="checkbox" name="permissions[<?php echo e($tableName); ?>][delete]" value="1" lay-skin="primary" <?php echo e($p && $p->can_delete ? 'checked' : ''); ?>></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-ok"></i> 保存权限</button>
                    <a href="<?php echo e(route('user-groups.index')); ?>" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>layui.use('form', function(){ var form = layui.form; form.render('checkbox'); });</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/permissions/edit.blade.php ENDPATH**/ ?>