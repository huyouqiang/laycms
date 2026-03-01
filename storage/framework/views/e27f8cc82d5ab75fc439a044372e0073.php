<?php $__env->startSection('title', '编辑表单'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">编辑表单</div>
    <div class="card-body">
        <form action="<?php echo e(route('forms.update', $form)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="mb-3">
                <label class="form-label">表单分组</label>
                <select name="form_group_id" class="form-select" required>
                    <?php $__currentLoopData = $formGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($g->id); ?>" <?php echo e(old('form_group_id', $form->form_group_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">表单名称</label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $form->name)); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">数据表名</label>
                <input type="text" name="table_name" class="form-control" value="<?php echo e(old('table_name', $form->table_name)); ?>" readonly style="background:#f5f5f5">
            </div>
            <div class="mb-3">
                <label class="form-label">描述</label>
                <input type="text" name="description" class="form-control" value="<?php echo e(old('description', $form->description)); ?>">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="<?php echo e(route('forms.index')); ?>" class="btn btn-secondary">返回</a>
            <a href="<?php echo e(route('form-fields.index', $form)); ?>" class="btn btn-outline-primary">配置字段</a>
        </form>
    </div>
</div>

<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>关联表单（MySQL 外键）</span>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#relationModal" id="btnAddRelation">添加关联</button>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>本表字段</th><th>关联表单</th><th>关联字段</th><th>操作</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $form->relations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($rel->formField?->field_name); ?></td>
                    <td><?php echo e($rel->relatedForm?->name); ?>（<?php echo e($rel->relatedForm?->table_name); ?>）</td>
                    <td><?php echo e($rel->related_field_name); ?></td>
                    <td>
                        <form action="<?php echo e(route('form-relations.destroy', $rel)); ?>" method="POST" class="d-inline" onsubmit="return confirm('确定删除此关联？将移除数据库外键约束。');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(($form->relations ?? collect())->isEmpty()): ?>
                <tr><td colspan="4" class="text-secondary">暂无关联</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="relationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加关联</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="relationForm">
                <div class="modal-body">
                    <input type="hidden" name="form_id" value="<?php echo e($form->id); ?>">
                    <div class="mb-3">
                        <label class="form-label">本表字段</label>
                        <div class="d-flex gap-2">
                            <select name="form_field_id" id="formFieldId" class="form-select flex-grow-1">
                                <option value="">-- 新建字段 --</option>
                                <?php $__currentLoopData = $form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$f->relation): ?>
                                <option value="<?php echo e($f->id); ?>" data-name="<?php echo e($f->field_name); ?>"><?php echo e($f->label); ?>（<?php echo e($f->field_name); ?>）</option>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="text" name="field_name" id="fieldName" class="form-control" placeholder="新字段名（如 category_id）" style="max-width:160px">
                        </div>
                        <small class="text-secondary">选择已有字段或输入新字段名</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">关联表单</label>
                        <select name="related_form_id" id="relatedFormId" class="form-select" required>
                            <option value="">请选择</option>
                            <?php $__currentLoopData = $otherForms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $of): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($of->id); ?>" data-table="<?php echo e($of->table_name); ?>"><?php echo e($of->name); ?>（<?php echo e($of->table_name); ?>）</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">关联字段</label>
                        <select name="related_field_name" id="relatedFieldName" class="form-select" required>
                            <option value="id">id</option>
                        </select>
                        <small class="text-secondary">选择关联表中的字段（通常为 id）</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<?php $__env->startPush('scripts'); ?>
<script>
$(function(){
    $('#relatedFormId').on('change', function(){
        var fid = $(this).val();
        var sel = $('#relatedFieldName').empty().append('<option value="id">id</option>');
        if (!fid) return;
        var url = '<?php echo e(url("/forms")); ?>/' + fid + '/related-columns';
        $.get(url, function(res){
            (res.columns || []).forEach(function(col){
                if (col !== 'id') sel.append($('<option></option>').val(col).text(col));
            });
        });
    });
    $('#formFieldId').on('change', function(){
        var v = $(this).val();
        $('#fieldName').prop('disabled', !!v).val(v ? $(this).find('option:selected').data('name') : '');
    });
    $('#relationForm').on('submit', function(e){
        e.preventDefault();
        var fd = new FormData(this);
        if (!fd.get('form_field_id') && !fd.get('field_name')) { alert('请选择已有字段或输入新字段名'); return; }
        if (!fd.get('related_form_id')) { alert('请选择关联表单'); return; }
        var data = { _token: '<?php echo e(csrf_token()); ?>', form_id: fd.get('form_id'), related_form_id: fd.get('related_form_id'), related_field_name: fd.get('related_field_name') || 'id' };
        if (fd.get('form_field_id')) data.form_field_id = fd.get('form_field_id'); else data.field_name = fd.get('field_name');
        $.post('<?php echo e(route("form-relations.store")); ?>', data).done(function(){ location.reload(); }).fail(function(x){ alert(x.responseJSON?.message || (x.responseJSON?.errors ? JSON.stringify(x.responseJSON.errors) : '添加失败')); });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/forms/edit.blade.php ENDPATH**/ ?>