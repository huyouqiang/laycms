@php
    $name = $field->field_name;
    $opts = $field->getOptionsArray();
@endphp
@switch($field->form_control)
    @case('textarea')
        <textarea name="{{ $name }}" class="form-control" placeholder="{{ $field->label }}">{{ $value }}</textarea>
        @break
    @case('number')
        <input type="number" name="{{ $name }}" value="{{ $value }}" class="form-control" placeholder="{{ $field->label }}">
        @break
    @case('date')
        <input type="date" name="{{ $name }}" value="{{ $value }}" class="form-control">
        @break
    @case('datetime')
        @php
            $dtVal = $value ? (strlen($value) > 10 ? substr($value, 0, 19) : $value) : '';
            if ($dtVal && strpos($dtVal, ' ') !== false) $dtVal = str_replace(' ', 'T', $dtVal);
        @endphp
        <input type="datetime-local" name="{{ $name }}" value="{{ $dtVal }}" class="form-control" step="1">
        @break
    @case('select')
        <select name="{{ $name }}" class="form-select" {{ $field->is_required ? 'required' : '' }}>
            <option value="">请选择</option>
            @foreach($opts as $k => $v)
            <option value="{{ $k }}" {{ (string)$value === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
        @break
    @case('radio')
        <div class="d-flex gap-3 flex-wrap">
            @foreach($opts as $k => $v)
            <div class="form-check">
                <input type="radio" name="{{ $name }}" value="{{ $k }}" class="form-check-input" id="radio_{{ $name }}_{{ $k }}" {{ (string)$value === (string)$k ? 'checked' : '' }}>
                <label class="form-check-label" for="radio_{{ $name }}_{{ $k }}">{{ $v }}</label>
            </div>
            @endforeach
        </div>
        @break
    @case('checkbox')
        <div class="d-flex gap-3 flex-wrap">
            @foreach($opts as $k => $v)
            <div class="form-check">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $k }}" class="form-check-input" id="cb_{{ $name }}_{{ $k }}"
                    {{ is_array($value) && in_array($k, $value) ? 'checked' : (is_string($value) && in_array($k, json_decode($value, true) ?? []) ? 'checked' : '') }}>
                <label class="form-check-label" for="cb_{{ $name }}_{{ $k }}">{{ $v }}</label>
            </div>
            @endforeach
        </div>
        @break
    @case('editor')
        <textarea name="{{ $name }}" id="ckeditor-{{ $name }}" class="form-control ckeditor-field" style="min-height:200px">{{ $value }}</textarea>
        @break
    @case('file')
        <div class="file-upload-wrap d-flex align-items-center gap-2 flex-wrap" data-name="{{ $name }}">
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="file-path-input" {{ $field->is_required ? 'required' : '' }}>
            <label class="btn btn-sm btn-outline-primary mb-0">
                <i class="bi bi-upload me-1"></i>
                <input type="file" class="file-upload-input d-none" accept="*/*">选择文件
            </label>
            <span class="file-path-display text-secondary small">{!! $value ? '<a href="'.asset($value).'" target="_blank" rel="noopener">'.$value.'</a>' : '未上传' !!}</span>
            <a href="javascript:;" class="file-clear-link small" style="{{ $value ? '' : 'display:none' }}"><i class="bi bi-x-circle me-1"></i>清除</a>
        </div>
        @break
    @case('relation')
        @php
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
        @endphp
        <div class="relation-autocomplete" data-table="{{ $rel && $rel->relatedForm ? $rel->relatedForm->table_name : '' }}" data-ref="{{ $refCol }}" data-display="{{ $displayCol }}" data-name="{{ $name }}" data-required="{{ $field->is_required ? '1' : '0' }}">
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="relation-value" {{ $field->is_required ? 'required' : '' }}>
            <input type="text" class="form-control relation-input" placeholder="输入搜索或选择" value="{{ $initLabel }}" autocomplete="off">
            <div class="relation-dropdown list-group"></div>
        </div>
        @break
    @default
        <input type="text" name="{{ $name }}" value="{{ is_array($value) ? json_encode($value) : $value }}" class="form-control" placeholder="{{ $field->label }}">
@endswitch
