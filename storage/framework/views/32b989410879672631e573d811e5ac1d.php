<?php $__env->startSection('title', '表单分组'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal" id="btnAdd"><i class="bi bi-plus-lg me-1"></i>新建分组</button>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>ID</th><th>分组名称</th><th>排序</th><th>表单数</th><th>操作</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($g->id); ?></td>
                    <td><?php echo e($g->name); ?></td>
                    <td><?php echo e($g->sort_order); ?></td>
                    <td><?php echo e($g->forms_count); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-group" data-group='<?php echo json_encode($g, 15, 512) ?>'><i class="bi bi-pencil me-1"></i>编辑</button>
                        <form action="<?php echo e(route('form-groups.destroy', $g)); ?>" method="POST" class="d-inline" onsubmit="return confirm('确定删除？');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" <?php echo e($g->forms_count > 0 ? 'disabled title="该分组下有表单"' : ''); ?>><i class="bi bi-trash me-1"></i>删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="groupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalTitle">新建分组</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="groupForm">
                <div class="modal-body">
                    <input type="hidden" name="group_id" id="groupId">
                    <div class="mb-3">
                        <label class="form-label">分组名称</label>
                        <input type="text" name="name" id="groupName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">排序</label>
                        <input type="number" name="sort_order" id="groupSortOrder" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>取消</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function(){
    $('#btnAdd').on('click', function(){
        $('#groupModalTitle').text('新建分组');
        $('#groupId').val('');
        $('#groupForm')[0].reset();
        $('#groupSortOrder').val(0);
    });
    $('.edit-group').on('click', function(){
        var g = $(this).data('group');
        $('#groupModalTitle').text('编辑分组');
        $('#groupId').val(g.id);
        $('#groupName').val(g.name);
        $('#groupSortOrder').val(g.sort_order || 0);
        new bootstrap.Modal(document.getElementById('groupModal')).show();
    });
    $('#groupForm').on('submit', function(e){
        e.preventDefault();
        var gid = $('#groupId').val();
        var data = { _token: '<?php echo e(csrf_token()); ?>', _method: gid ? 'PUT' : 'POST', name: $('#groupName').val(), sort_order: $('#groupSortOrder').val() || 0 };
        $.post(gid ? '/form-groups/'+gid : '<?php echo e(route("form-groups.store")); ?>', data).done(function(){ location.reload(); }).fail(function(x){ alert(x.responseJSON?.msg || '保存失败'); });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/form-groups/index.blade.php ENDPATH**/ ?>