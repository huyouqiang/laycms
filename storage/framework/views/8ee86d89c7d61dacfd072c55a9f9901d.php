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

<?php $__env->startPush('scripts'); ?>
<?php if($form->fields->contains('form_control', 'editor')): ?>
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full/lang/zh-cn.js"></script>
<?php endif; ?>
<script>
$(function(){
    <?php if($form->fields->contains('form_control', 'editor')): ?>
    $('.ckeditor-field').each(function(){
        CKEDITOR.replace(this.id, {
            versionCheck: false,
            height: 300,
            language: 'zh-cn',
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ]
        });
    });
    <?php endif; ?>
    var searchTimer;
    $('.relation-autocomplete').each(function(){
        var $wrap = $(this);
        var $input = $wrap.find('.relation-input');
        var $value = $wrap.find('.relation-value');
        var $dropdown = $wrap.find('.relation-dropdown');
        var table = $wrap.data('table');
        var ref = $wrap.data('ref');
        var display = $wrap.data('display');
        var required = $wrap.data('required') == 1;

        function fetchOptions(q){
            if (!table) return;
            $.get('<?php echo e(route("table-data.relation-options")); ?>', { table: table, ref: ref, display: display, q: q || '' }, function(res){
                $dropdown.empty();
                (res.data || []).forEach(function(item){
                    var $a = $('<a href="javascript:;" class="list-group-item list-group-item-action"></a>').text(item.label).data('value', item.value);
                    $a.on('click', function(){ $value.val($(this).data('value')); $input.val($(this).text()); $dropdown.hide(); });
                    $dropdown.append($a);
                });
                if ((res.data || []).length === 0) $dropdown.append('<div class="list-group-item text-secondary">无匹配项</div>');
                $dropdown.show();
            });
        }

        $input.on('input focus', function(){
            var q = $(this).val();
            if (q === '') $value.val('');
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function(){ fetchOptions(q); }, 200);
        }).on('blur', function(){
            setTimeout(function(){ $dropdown.hide(); }, 150);
        });

        $input.on('keydown', function(e){
            if (e.key === 'Escape') { $dropdown.hide(); }
        });
    });

    $('.file-upload-input').on('change', function(){
        var $input = $(this);
        var $wrap = $input.closest('.file-upload-wrap');
        var $pathInput = $wrap.find('.file-path-input');
        var $display = $wrap.find('.file-path-display');
        var file = this.files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '<?php echo e(csrf_token()); ?>');
        $display.text('上传中...');
        $.ajax({
            url: '<?php echo e(route("upload.store")); ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function(res){
            if (res.code === 0) {
                $pathInput.val(res.path);
                var baseUrl = '<?php echo e(url("/")); ?>'.replace(/\/$/, '');
                $display.html('<a href="' + baseUrl + '/' + res.path.replace(/^\//, '') + '" target="_blank" rel="noopener">' + res.path + '</a>');
                $wrap.find('.file-clear-link').show();
            } else {
                $display.text(res.msg || '上传失败');
            }
        }).fail(function(x){
            var msg = x.responseJSON && x.responseJSON.msg ? x.responseJSON.msg : (x.responseJSON && x.responseJSON.errors ? JSON.stringify(x.responseJSON.errors) : '上传失败');
            $display.text(msg);
        });
        $input.val('');
    });

    $(document).on('click', '.file-clear-link', function(){
        var $wrap = $(this).closest('.file-upload-wrap');
        $wrap.find('.file-path-input').val('');
        $wrap.find('.file-path-display').text('未上传');
        $(this).hide();
    });

    $('form').on('submit', function(){
        if (typeof CKEDITOR !== 'undefined') {
            for (var i in CKEDITOR.instances) { CKEDITOR.instances[i].updateElement(); }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/table-data/form.blade.php ENDPATH**/ ?>