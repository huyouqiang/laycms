<?php $__env->startSection('title', '表单分组'); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card">
    <div class="layui-card-header">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAdd"><i class="layui-icon layui-icon-add-1"></i> 新建分组</button>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
            <thead><tr><th>ID</th><th>分组名称</th><th>排序</th><th>表单数</th><th>操作</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($g->id); ?></td>
                    <td><?php echo e($g->name); ?></td>
                    <td><?php echo e($g->sort_order); ?></td>
                    <td><?php echo e($g->forms_count); ?></td>
                    <td>
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary edit-group" data-group='<?php echo json_encode($g, 15, 512) ?>'><i class="layui-icon layui-icon-edit"></i> 编辑</button>
                        <form action="<?php echo e(route('form-groups.destroy', $g)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-primary" <?php echo e($g->forms_count > 0 ? 'disabled title="该分组下有表单"' : ''); ?>><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div id="groupModalBox" style="display:none;">
    <form class="layui-form" id="groupForm" style="padding:20px;">
        <input type="hidden" name="group_id" id="groupId">
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">分组名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" id="groupName" class="layui-input" required>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort_order" id="groupSortOrder" class="layui-input" value="0" min="0">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal">保存</button>
                <button type="button" class="layui-btn layui-btn-primary" id="groupModalClose">取消</button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
layui.use(['jquery', 'layer', 'form'], function(){
    var $ = layui.$;
    var layer = layui.layer;
    var form = layui.form;
    var groupModalIndex = 0;

    function openGroupModal(title, editData){
        var html = $('#groupModalBox').html();
        groupModalIndex = layer.open({
            type: 1,
            title: title,
            area: ['420px', '320px'],
            content: html,
            success: function(layero, index){
                if (editData) {
                    layero.find('#groupId').val(editData.id);
                    layero.find('#groupName').val(editData.name);
                    layero.find('#groupSortOrder').val(editData.sort_order || 0);
                } else {
                    layero.find('#groupId').val('');
                    layero.find('#groupForm')[0].reset();
                    layero.find('#groupSortOrder').val(0);
                }
                layero.find('#groupModalClose').on('click', function(){ layer.close(index); });
                layero.find('#groupForm').on('submit', function(e){
                    e.preventDefault();
                    var gid = layero.find('#groupId').val();
                    var data = { _token: '<?php echo e(csrf_token()); ?>', _method: gid ? 'PUT' : 'POST', name: layero.find('#groupName').val(), sort_order: layero.find('#groupSortOrder').val() || 0 };
                    $.post(gid ? '/form-groups/'+gid : '<?php echo e(route("form-groups.store")); ?>', data).done(function(){ layer.close(index); location.reload(); }).fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.msg ? x.responseJSON.msg : '保存失败'); });
                });
            }
        });
    }

    $('#btnAdd').on('click', function(){ openGroupModal('新建分组', null); });
    $(document).on('click', '.edit-group', function(){ openGroupModal('编辑分组', $(this).data('group')); });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/form-groups/index.blade.php ENDPATH**/ ?>