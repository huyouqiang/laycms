<?php
    $name = $field->field_name;
    $opts = $field->getOptionsArray();
?>
<?php switch($field->form_control):
    case ('textarea'): ?>
        <textarea name="<?php echo e($name); ?>" class="layui-textarea" placeholder="<?php echo e($field->label); ?>"><?php echo e($value); ?></textarea>
        <?php break; ?>
    <?php case ('number'): ?>
        <input type="number" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="layui-input" placeholder="<?php echo e($field->label); ?>">
        <?php break; ?>
    <?php case ('date'): ?>
        <input type="date" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="layui-input">
        <?php break; ?>
    <?php case ('datetime'): ?>
        <?php
            $dtVal = $value ? (strlen($value) > 10 ? substr($value, 0, 19) : $value) : '';
            if ($dtVal && strpos($dtVal, ' ') !== false) $dtVal = str_replace(' ', 'T', $dtVal);
        ?>
        <input type="datetime-local" name="<?php echo e($name); ?>" value="<?php echo e($dtVal); ?>" class="layui-input" step="1">
        <?php break; ?>
    <?php case ('select'): ?>
        <select name="<?php echo e($name); ?>" lay-ignore <?php echo e($field->is_required ? 'required' : ''); ?>>
            <option value="">请选择</option>
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php echo e((string)$value === (string)$k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php break; ?>
    <?php case ('radio'): ?>
        <div style="display:flex;gap:15px;flex-wrap:wrap;">
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="radio" name="<?php echo e($name); ?>" value="<?php echo e($k); ?>" title="<?php echo e($v); ?>" <?php echo e((string)$value === (string)$k ? 'checked' : ''); ?>>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php break; ?>
    <?php case ('checkbox'): ?>
        <div style="display:flex;gap:15px;flex-wrap:wrap;">
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="checkbox" name="<?php echo e($name); ?>[]" value="<?php echo e($k); ?>" lay-skin="primary" title="<?php echo e($v); ?>"
                <?php echo e(is_array($value) && in_array($k, $value) ? 'checked' : (is_string($value) && in_array($k, json_decode($value, true) ?? []) ? 'checked' : '')); ?>>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php break; ?>
    <?php case ('editor'): ?>
        <textarea name="<?php echo e($name); ?>" id="ckeditor-<?php echo e($name); ?>" class="ckeditor-field" style="min-height:200px"><?php echo e($value); ?></textarea>
        <?php break; ?>
    <?php case ('file'): ?>
        <div class="file-upload-wrap" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;" data-name="<?php echo e($name); ?>">
            <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="file-path-input" <?php echo e($field->is_required ? 'required' : ''); ?>>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-primary layui-btn-fluid" style="width:auto;">
                <i class="layui-icon layui-icon-upload"></i> 选择文件
                <input type="file" class="file-upload-input" accept="*/*" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;">
            </button>
            <span class="file-path-display" style="color:#999;font-size:12px;"><?php echo $value ? '<a href="'.asset($value).'" target="_blank" rel="noopener">'.$value.'</a>' : '未上传'; ?></span>
            <a href="javascript:;" class="file-clear-link layui-btn layui-btn-sm layui-btn-primary" style="<?php echo e($value ? '' : 'display:none'); ?>"><i class="layui-icon layui-icon-close"></i> 清除</a>
        </div>
        <?php break; ?>
    <?php case ('relation'): ?>
        <?php
            $rel = $field->relation ?? null;
            $refCol = 'id';
            $displayCol = 'id';
            $initLabel = '';
            if ($rel && $rel->relatedForm && \Illuminate\Support\Facades\Schema::hasTable($rel->relatedForm->table_name)) {
                $refCol = $rel->related_field_name ?: 'id';
                $displayCol = \Illuminate\Support\Arr::first($rel->relatedForm->fields ?? [], fn($f) => $f->is_list_visible)?->field_name ?? $refCol;
                if ($value !== '' && $value !== null) {
                    $initRow = \Illuminate\Support\Facades\DB::table($rel->relatedForm->table_name)->where($refCol, $value)->first();
                    $initLabel = $initRow && isset($initRow->{$displayCol}) ? $initRow->{$displayCol} : (string)$value;
                }
            }
        ?>
        <div class="relation-autocomplete" data-table="<?php echo e($rel && $rel->relatedForm ? $rel->relatedForm->table_name : ''); ?>" data-ref="<?php echo e($refCol); ?>" data-display="<?php echo e($displayCol); ?>" data-name="<?php echo e($name); ?>" data-required="<?php echo e($field->is_required ? '1' : '0'); ?>">
            <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="relation-value" <?php echo e($field->is_required ? 'required' : ''); ?>>
            <input type="text" class="layui-input relation-input" placeholder="输入搜索或选择" value="<?php echo e($initLabel); ?>" autocomplete="off">
            <div class="relation-dropdown" style="position:absolute;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;z-index:9999;display:none;margin-top:2px;border:1px solid #e6e6e6;border-radius:2px;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.1);"></div>
        </div>
        <?php break; ?>
    <?php default: ?>
        <input type="text" name="<?php echo e($name); ?>" value="<?php echo e(is_array($value) ? json_encode($value) : $value); ?>" class="layui-input" placeholder="<?php echo e($field->label); ?>">
<?php endswitch; ?>
<?php /**PATH /opt/homebrew/var/www/laycms/resources/views/table-data/field-control.blade.php ENDPATH**/ ?>