<?php $__env->startSection('title', '编辑表单'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('warning')): ?>
<div style="margin-bottom:15px;">
    <blockquote class="layui-elem-quote layui-quote-nm layui-bg-green"><?php echo e(session('warning')); ?></blockquote>
</div>
<?php endif; ?>
<div id="jsWarningBlock" style="margin-bottom:15px;display:none;">
    <blockquote class="layui-elem-quote layui-quote-nm layui-bg-green" id="jsWarningText"></blockquote>
</div>
<style>
.add-index-form .layui-form-item .layui-input-block { width: 220px; position: relative; min-width: 0; }
.add-index-form .layui-form-select { width: 100% !important; min-width: 100% !important; position: relative; display: block; overflow: visible; }
.add-index-form .layui-form-select .layui-input { width: 100%; box-sizing: border-box; padding-right: 30px; }
/* 下拉箭头固定在输入框右侧，稍向下对齐 */
.add-index-form .layui-form-select .layui-edge {
    right: 10px !important; left: auto !important; top: 50% !important;
    margin-top: 15px !important; position: absolute !important;
}
</style>
<div class="layui-card">
    <div class="layui-card-header">编辑表单</div>
    <div class="layui-card-body">
        <form class="layui-form" action="<?php echo e(route('forms.update', $form)); ?>" method="POST" style="max-width:600px;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单分组</label>
                <div class="layui-input-block">
                    <select name="form_group_id" required>
                        <?php $__currentLoopData = $formGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php echo e(old('form_group_id', $form->form_group_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" value="<?php echo e(old('name', $form->name)); ?>" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">数据表名</label>
                <div class="layui-input-block">
                    <input type="text" name="table_name" class="layui-input" value="<?php echo e(old('table_name', $form->table_name)); ?>" readonly style="background:#f5f5f5;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" name="description" class="layui-input" value="<?php echo e(old('description', $form->description)); ?>">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-ok"></i> 保存</button>
                    <a href="<?php echo e(route('forms.index')); ?>" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                    <a href="<?php echo e(route('form-fields.index', $form)); ?>" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-set"></i> 配置字段</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<div class="layui-card" style="margin-top:20px;">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span>关联表单（MySQL 外键）</span>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAddRelation"><i class="layui-icon layui-icon-link"></i> 添加关联</button>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
            <thead><tr><th>本表字段</th><th>关联表单</th><th>关联字段</th><th>操作</th></tr></thead>
            <tbody>
                <?php $__currentLoopData = $form->relations ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($rel->formField?->field_name); ?></td>
                    <td><?php echo e($rel->relatedForm?->name); ?>（<?php echo e($rel->relatedForm?->table_name); ?>）</td>
                    <td><?php echo e($rel->related_field_name); ?></td>
                    <td>
                        <form action="<?php echo e(route('form-relations.destroy', $rel)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('确定删除此关联？将移除数据库外键约束。');">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(($form->relations ?? collect())->isEmpty()): ?>
                <tr><td colspan="4" style="color:#999;">暂无关联</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="layui-card" style="margin-top:20px;">
    <div class="layui-card-header">添加索引</div>
    <div class="layui-card-body">
        <?php if(!empty($tableIndexes)): ?>
        <div style="margin-bottom:20px;">
            <label class="layui-form-label" style="width:auto;padding:0 10px 0 0;">已添加的索引</label>
            <table class="layui-table">
                <thead><tr><th>索引名称</th><th>对应字段</th><th>索引类型</th><th>操作</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $tableIndexes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($idx['name']); ?></td>
                        <td><?php echo e($idx['column']); ?></td>
                        <td><?php echo e($idx['type']); ?></td>
                        <td>
                            <?php if($idx['name'] !== 'PRIMARY'): ?>
                            <form action="<?php echo e(route('forms.drop-index', $form)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('确定删除索引 <?php echo e($idx['name']); ?>？');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <input type="hidden" name="index_name" value="<?php echo e($idx['name']); ?>">
                                <button type="submit" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                            </form>
                            <?php else: ?>
                            <span style="color:#999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php if(!empty($tableColumns)): ?>
        <form class="layui-form add-index-form" action="<?php echo e(route('forms.add-index', $form)); ?>" method="POST" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <?php echo csrf_field(); ?>
            <div class="layui-form-item" style="margin-bottom:0;min-width:200px;">
                <label class="layui-form-label">选择字段</label>
                <div class="layui-input-block" style="margin-left:100px;">
                    <select name="column_name" required>
                        <option value="">请选择要添加索引的字段</option>
                        <?php $__currentLoopData = $tableColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($col); ?>"><?php echo e($col); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="margin-bottom:0;">
                <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-add-circle"></i> 添加索引</button>
            </div>
        </form>
        <p style="color:#999;margin-top:10px;font-size:12px;">为数据表 <?php echo e($form->table_name); ?> 的字段添加 MySQL 索引，以提升查询性能。</p>
        <?php else: ?>
        <p style="color:#999;margin:0;">数据表 <?php echo e($form->table_name); ?> 暂无可用字段。</p>
        <?php endif; ?>
    </div>
</div>

<div id="relationModalBox" style="display:none;">
    <form class="layui-form" id="relationForm" style="padding:20px;">
        <input type="hidden" name="form_id" value="<?php echo e($form->id); ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">本表字段</label>
            <div class="layui-input-block" style="display:flex;gap:10px;">
                <select name="form_field_id" id="formFieldId" style="flex:1;">
                    <option value="">-- 新建字段 --</option>
                    <?php $__currentLoopData = $form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!$f->relation): ?>
                    <option value="<?php echo e($f->id); ?>" data-name="<?php echo e($f->field_name); ?>"><?php echo e($f->label); ?>（<?php echo e($f->field_name); ?>）</option>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="text" name="field_name" id="fieldName" class="layui-input" placeholder="新字段名（如 category_id）" style="max-width:160px;">
            </div>
            <div class="layui-form-mid layui-word-aux">选择已有字段或输入新字段名</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">关联表单</label>
            <div class="layui-input-block">
                <select name="related_form_id" id="relatedFormId" lay-filter="relationRelatedForm" required>
                    <option value="">请选择</option>
                    <?php $__currentLoopData = $otherForms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $of): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($of->id); ?>" data-table="<?php echo e($of->table_name); ?>"><?php echo e($of->name); ?>（<?php echo e($of->table_name); ?>）</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">关联字段</label>
            <div class="layui-input-block">
                <input type="hidden" name="related_field_name" value="id">
                <input type="text" class="layui-input" value="id" readonly style="background:#f5f5f5;">
                <div class="layui-form-mid layui-word-aux">固定为关联表主键 id</div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-add-1"></i> 添加</button>
                <button type="button" class="layui-btn layui-btn-primary" id="relationModalClose">取消</button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update')): ?>
<?php $__env->startPush('scripts'); ?>
<script>
(function(){
    var w = sessionStorage.getItem('formsEditWarning');
    if (w) {
        sessionStorage.removeItem('formsEditWarning');
        var el = document.getElementById('jsWarningText');
        var block = document.getElementById('jsWarningBlock');
        if (el && block) { el.textContent = w; block.style.display = ''; }
    }
})();
layui.use(['jquery', 'layer', 'form'], function(){
    var $ = layui.$;
    var layer = layui.layer;
    var form = layui.form;
    form.render('select');

    form.on('select(relationRelatedForm)', function(data){
        // 关联字段固定为 id，无需根据关联表单切换
    });

    $('#btnAddRelation').on('click', function(){
        var html = $('#relationModalBox').html();
        layer.open({
            type: 1,
            title: '添加关联',
            area: ['520px', '420px'],
            content: html,
            success: function(layero, index){
                form.render('select');
                layero.find('#relationModalClose').on('click', function(){ layer.close(index); });
                layero.find('#formFieldId').on('change', function(){
                    var v = $(this).val();
                    layero.find('#fieldName').prop('disabled', !!v).val(v ? $(this).find('option:selected').data('name') : '');
                });
                layero.find('#relationForm').on('submit', function(e){
                    e.preventDefault();
                    var fd = new FormData(this);
                    if (!fd.get('form_field_id') && !fd.get('field_name')) { layer.msg('请选择已有字段或输入新字段名'); return; }
                    if (!fd.get('related_form_id')) { layer.msg('请选择关联表单'); return; }
                    var data = { _token: '<?php echo e(csrf_token()); ?>', form_id: fd.get('form_id'), related_form_id: fd.get('related_form_id'), related_field_name: fd.get('related_field_name') || 'id' };
                    if (fd.get('form_field_id')) data.form_field_id = fd.get('form_field_id'); else data.field_name = fd.get('field_name');
                    $.post('<?php echo e(route("form-relations.store")); ?>', data)
                        .done(function(data){
                            if (data && data.redirect) {
                                if (data.warning) sessionStorage.setItem('formsEditWarning', data.warning);
                                else if (data.code === 1 && data.msg) sessionStorage.setItem('formsEditWarning', data.msg);
                                window.location = data.redirect;
                                return;
                            }
                            layer.close(index);
                            location.reload();
                        })
                        .fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.message ? x.responseJSON.message : (x.responseJSON && x.responseJSON.errors ? JSON.stringify(x.responseJSON.errors) : '添加失败')); });
                });
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/forms/edit.blade.php ENDPATH**/ ?>