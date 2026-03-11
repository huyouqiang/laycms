<?php $__env->startSection('title', '字段配置 - ' . $form->name); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('warning')): ?>
<div style="margin-bottom:15px;" class="warning-block">
    <blockquote class="layui-elem-quote layui-quote-nm layui-bg-green"><?php echo e(session('warning')); ?></blockquote>
</div>
<?php endif; ?>
<div id="jsWarningBlock" style="margin-bottom:15px;display:none;">
    <blockquote class="layui-elem-quote layui-quote-nm layui-bg-green" id="jsWarningText"></blockquote>
</div>
<div class="layui-card">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span><?php echo e($form->name); ?> - 字段配置</span>
        <div>
            <a href="<?php echo e(route('table-data.index', $form->table_name)); ?>" class="layui-btn layui-btn-sm layui-btn-primary"><i class="layui-icon layui-icon-table"></i> 数据管理</a>
            <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAdd"><i class="layui-icon layui-icon-add-1"></i> 添加字段</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
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
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary edit-field" data-field='<?php echo json_encode($field, 15, 512) ?>'><i class="layui-icon layui-icon-edit"></i> 编辑</button>
                        <?php endif; ?>
                        <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'delete')): ?>
                        <form action="<?php echo e(route('form-fields.destroy', $field)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<div id="fieldModalBox" style="display:none;">
    <form class="layui-form" id="fieldForm" style="padding:20px;">
        <input type="hidden" name="form_id" value="<?php echo e($form->id); ?>">
        <input type="hidden" name="field_id" id="fieldId">
        <div class="layui-form-item" id="fieldNameWrap">
            <label class="layui-form-label">字段名</label>
            <div class="layui-input-block">
                <input type="text" name="field_name" id="fieldName" class="layui-input" placeholder="小写字母数字下划线">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">标签</label>
            <div class="layui-input-block">
                <input type="text" name="label" id="fieldLabel" class="layui-input" required placeholder="显示名称">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">表单控件</label>
            <div class="layui-input-block">
                <select name="form_control" id="formControl" required>
                    <option value="input">单行文本 (VARCHAR(255))</option>
                    <option value="input_bigint">单行文本 (BIGINT)</option>
                    <option value="textarea">多行文本 (TEXT)</option>
                    <option value="number">数字 (BIGINT)</option>
                    <option value="date">日期 (DATE)</option>
                    <option value="datetime">日期时间 (DATETIME)</option>
                    <option value="select">下拉框 (VARCHAR(255))</option>
                    <option value="radio">单选框 (VARCHAR(255))</option>
                    <option value="checkbox">多选框 (VARCHAR(255))</option>
                    <option value="file">文件 (VARCHAR(500))</option>
                    <option value="editor">富文本 (TEXT)</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">选项(JSON)</label>
            <div class="layui-input-block">
                <textarea name="options" id="fieldOptions" class="layui-textarea" placeholder='{"1":"选项1","2":"选项2"}' rows="2"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="checkbox" name="is_required" id="isRequired" lay-skin="primary" title="必填">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="checkbox" name="is_list_visible" id="isListVisible" lay-skin="primary" title="列表显示" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal">保存</button>
                <button type="button" class="layui-btn layui-btn-primary" id="fieldModalClose">取消</button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){
    var w = sessionStorage.getItem('formFieldsWarning');
    if (w) {
        sessionStorage.removeItem('formFieldsWarning');
        document.getElementById('jsWarningText').textContent = w;
        document.getElementById('jsWarningBlock').style.display = '';
    }
})();
layui.use(['jquery', 'layer', 'form'], function(){
    var $ = layui.$;
    var layer = layui.layer;
    var form = layui.form;
    var fieldModalIndex = 0;

    function openFieldModal(title, editData){
        var html = $('#fieldModalBox').html();
        fieldModalIndex = layer.open({
            type: 1,
            title: title,
            area: ['500px', '560px'],
            content: html,
            success: function(layero, index){
                if (editData) {
                    layero.find('#fieldId').val(editData.id);
                    layero.find('#fieldName').val(editData.field_name).prop('readonly', true);
                    layero.find('#fieldNameWrap').show();
                    layero.find('#fieldLabel').val(editData.label);
                    layero.find('#formControl').val(editData.form_control || 'input');
                    layero.find('#fieldOptions').val(editData.options || '');
                    layero.find('#isRequired').prop('checked', editData.is_required);
                    layero.find('#isListVisible').prop('checked', editData.is_list_visible !== false);
                } else {
                    layero.find('#fieldId').val('');
                    layero.find('#fieldName').val('').prop('readonly', false);
                    layero.find('#fieldNameWrap').show();
                    layero.find('#formControl').val('input');
                    layero.find('#isListVisible').prop('checked', true);
                }
                form.render('select');
                form.render('checkbox');
                layero.find('#fieldModalClose').on('click', function(){ layer.close(index); });
                layero.find('#fieldForm').on('submit', function(e){
                    e.preventDefault();
                    var fid = layero.find('#fieldId').val();
                    var formControlVal = layero.find('select[name="form_control"]').val() || layero.find('#formControl').val();
                    var data = { _token: '<?php echo e(csrf_token()); ?>', _method: fid ? 'PUT' : 'POST', form_id: layero.find('input[name="form_id"]').val(), field_name: layero.find('#fieldName').val(), label: layero.find('#fieldLabel').val(), form_control: formControlVal, options: layero.find('#fieldOptions').val(), is_required: layero.find('#isRequired').prop('checked') ? 1 : 0, is_list_visible: layero.find('#isListVisible').prop('checked') ? 1 : 0 };
                    $.post(fid ? '/form-fields/'+fid : '/form-fields', data)
                        .done(function(data){
                            if (data && data.redirect) {
                                if (data.warning) sessionStorage.setItem('formFieldsWarning', data.warning);
                                window.location = data.redirect;
                                return;
                            }
                            layer.close(index);
                            location.reload();
                        })
                        .fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.msg ? x.responseJSON.msg : '保存失败'); });
                });
            }
        });
    }

    $('#btnAdd').on('click', function(){ openFieldModal('添加字段', null); });
    $(document).on('click', '.edit-field', function(){ openFieldModal('编辑字段', $(this).data('field')); });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/form-fields/index.blade.php ENDPATH**/ ?>