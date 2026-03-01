<?php
    $name = $field->field_name;
    $opts = $field->getOptionsArray();
?>
<?php switch($field->form_control):
    case ('textarea'): ?>
        <textarea name="<?php echo e($name); ?>" class="form-control" placeholder="<?php echo e($field->label); ?>"><?php echo e($value); ?></textarea>
        <?php break; ?>
    <?php case ('number'): ?>
        <input type="number" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="form-control" placeholder="<?php echo e($field->label); ?>">
        <?php break; ?>
    <?php case ('date'): ?>
        <input type="date" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>" class="form-control">
        <?php break; ?>
    <?php case ('datetime'): ?>
        <?php
            $dtVal = $value ? (strlen($value) > 10 ? substr($value, 0, 19) : $value) : '';
            if ($dtVal && strpos($dtVal, ' ') !== false) $dtVal = str_replace(' ', 'T', $dtVal);
        ?>
        <input type="datetime-local" name="<?php echo e($name); ?>" value="<?php echo e($dtVal); ?>" class="form-control" step="1">
        <?php break; ?>
    <?php case ('select'): ?>
        <select name="<?php echo e($name); ?>" class="form-select" <?php echo e($field->is_required ? 'required' : ''); ?>>
            <option value="">请选择</option>
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php echo e((string)$value === (string)$k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php break; ?>
    <?php case ('radio'): ?>
        <div class="d-flex gap-3 flex-wrap">
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check">
                <input type="radio" name="<?php echo e($name); ?>" value="<?php echo e($k); ?>" class="form-check-input" id="radio_<?php echo e($name); ?>_<?php echo e($k); ?>" <?php echo e((string)$value === (string)$k ? 'checked' : ''); ?>>
                <label class="form-check-label" for="radio_<?php echo e($name); ?>_<?php echo e($k); ?>"><?php echo e($v); ?></label>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php break; ?>
    <?php case ('checkbox'): ?>
        <div class="d-flex gap-3 flex-wrap">
            <?php $__currentLoopData = $opts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check">
                <input type="checkbox" name="<?php echo e($name); ?>[]" value="<?php echo e($k); ?>" class="form-check-input" id="cb_<?php echo e($name); ?>_<?php echo e($k); ?>"
                    <?php echo e(is_array($value) && in_array($k, $value) ? 'checked' : (is_string($value) && in_array($k, json_decode($value, true) ?? []) ? 'checked' : '')); ?>>
                <label class="form-check-label" for="cb_<?php echo e($name); ?>_<?php echo e($k); ?>"><?php echo e($v); ?></label>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php break; ?>
    <?php case ('editor'): ?>
        <textarea name="<?php echo e($name); ?>" class="form-control" style="min-height:200px"><?php echo e($value); ?></textarea>
        <?php break; ?>
    <?php case ('relation'): ?>
        <?php
            $rel = $field->relation ?? null;
            $relOpts = [];
            if ($rel && $rel->relatedForm && \Illuminate\Support\Facades\Schema::hasTable($rel->relatedForm->table_name)) {
                $rows = \Illuminate\Support\Facades\DB::table($rel->relatedForm->table_name)->orderBy('id')->get();
                $refCol = $rel->related_field_name ?: 'id';
                $displayCol = \Illuminate\Support\Arr::first($rel->relatedForm->fields ?? [], fn($f) => $f->is_list_visible)?->field_name ?? $refCol;
                foreach ($rows as $r) {
                    $val = $r->{$refCol} ?? $r->id ?? '';
                    $label = isset($r->{$displayCol}) ? $r->{$displayCol} : $val;
                    $relOpts[$val] = $label;
                }
            }
        ?>
        <select name="<?php echo e($name); ?>" class="form-select" <?php echo e($field->is_required ? 'required' : ''); ?>>
            <option value="">请选择</option>
            <?php $__currentLoopData = $relOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php echo e((string)$value === (string)$k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php break; ?>
    <?php default: ?>
        <input type="text" name="<?php echo e($name); ?>" value="<?php echo e(is_array($value) ? json_encode($value) : $value); ?>" class="form-control" placeholder="<?php echo e($field->label); ?>">
<?php endswitch; ?>
<?php /**PATH /opt/homebrew/var/www/laycms/resources/views/table-data/field-control.blade.php ENDPATH**/ ?>