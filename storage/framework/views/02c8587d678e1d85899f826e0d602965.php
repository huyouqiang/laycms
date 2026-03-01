<?php $__env->startSection('title', '字段配置 - ' . $form->name); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?php echo e($form->name); ?> - 字段配置</span>
        <div>
            <a href="<?php echo e(route('table-data.index', $form->table_name)); ?>" class="btn btn-sm btn-outline-primary">数据管理</a>
            <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fieldModal" id="btnAdd">添加字段</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>排序</th><th>字段名</th><th>标签</th><th>表单控件</th><th>必填</th><th>列表显示</th><th>操作</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($field->sort_order); ?></td>
                    <td><?php echo e($field->field_name); ?></td>
                    <td><?php echo e($field->label); ?></td>
                    <td><?php echo e($field->form_control); ?></td>
                    <td><?php echo e($field->is_required ? '是' : '否'); ?></td>
                    <td><?php echo e($field->is_list_visible ? '是' : '否'); ?></td>
                    <td>
                        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-field" data-field='<?php echo json_encode($field, 15, 512) ?>'>编辑</button>
                        <?php endif; ?>
                        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'delete')): ?>
                        <form action="<?php echo e(route('form-fields.destroy', $field)); ?>" method="POST" class="d-inline" onsubmit="return confirm('确定删除？');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<div class="modal fade" id="fieldModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fieldModalTitle">添加字段</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fieldForm">
                <div class="modal-body">
                    <input type="hidden" name="form_id" value="<?php echo e($form->id); ?>">
                    <input type="hidden" name="field_id" id="fieldId">
                    <div class="mb-3" id="fieldNameWrap">
                        <label class="form-label">字段名</label>
                        <input type="text" name="field_name" id="fieldName" class="form-control" placeholder="小写字母数字下划线">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">标签</label>
                        <input type="text" name="label" id="fieldLabel" class="form-control" required placeholder="显示名称">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">表单控件</label>
                        <select name="form_control" id="formControl" class="form-select" required>
                            <option value="input">单行文本</option>
                            <option value="textarea">多行文本</option>
                            <option value="number">数字</option>
                            <option value="date">日期</option>
                            <option value="datetime">日期时间</option>
                            <option value="select">下拉框</option>
                            <option value="radio">单选框</option>
                            <option value="checkbox">多选框</option>
                            <option value="file">文件</option>
                            <option value="editor">富文本</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">选项(JSON)</label>
                        <textarea name="options" id="fieldOptions" class="form-control" placeholder='{"1":"选项1","2":"选项2"}' rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_required" id="isRequired" class="form-check-input">
                            <label class="form-check-label" for="isRequired">必填</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_list_visible" id="isListVisible" class="form-check-input" checked>
                            <label class="form-check-label" for="isListVisible">列表显示</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function(){
    var modalEl = document.getElementById('fieldModal');
    if (!modalEl) return;
    var fieldModal = new bootstrap.Modal(modalEl);
    $('#btnAdd').on('click', function(){
        $('#fieldModalTitle').text('添加字段');
        $('#fieldId').val('');
        $('#fieldName').val('').prop('readonly', false);
        $('#fieldNameWrap').show();
        $('#fieldForm')[0].reset();
        $('#isListVisible').prop('checked', true);
    });
    $('.edit-field').on('click', function(){
        var f = $(this).data('field');
        $('#fieldModalTitle').text('编辑字段');
        $('#fieldId').val(f.id);
        $('#fieldName').val(f.field_name).prop('readonly', true);
        $('#fieldNameWrap').hide();
        $('#fieldLabel').val(f.label);
        $('#formControl').val(f.form_control || 'input');
        $('#fieldOptions').val(f.options || '');
        $('#isRequired').prop('checked', f.is_required);
        $('#isListVisible').prop('checked', f.is_list_visible !== false);
        fieldModal.show();
    });
    $('#fieldForm').on('submit', function(e){
        e.preventDefault();
        var fid = $('#fieldId').val();
        var data = $(this).serializeArray();
        var obj = { _token: '<?php echo e(csrf_token()); ?>', _method: fid ? 'PUT' : 'POST' };
        data.forEach(function(i){ obj[i.name]=i.value; });
        if($('#isRequired').prop('checked')) obj.is_required=1;
        if($('#isListVisible').prop('checked')) obj.is_list_visible=1;
        $.post(fid ? '/form-fields/'+fid : '/form-fields', obj).done(function(){
            location.reload();
        }).fail(function(x){
            alert(x.responseJSON?.msg || '保存失败');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/form-fields/index.blade.php ENDPATH**/ ?>