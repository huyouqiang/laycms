@php
    $name = $field->field_name;
    $opts = $field->getOptionsArray();
@endphp
@switch($field->form_control)
    @case('textarea')
        <textarea name="{{ $name }}" class="layui-textarea" placeholder="{{ $field->label }}">{{ $value }}</textarea>
        @break
    @case('number')
        <input type="number" name="{{ $name }}" value="{{ $value }}" class="layui-input" placeholder="{{ $field->label }}">
        @break
    @case('date')
        <input type="date" name="{{ $name }}" value="{{ $value }}" class="layui-input">
        @break
    @case('datetime')
        @php
            $dtVal = $value ? (strlen($value) > 10 ? substr($value, 0, 19) : $value) : '';
            if ($dtVal && strpos($dtVal, ' ') !== false) $dtVal = str_replace(' ', 'T', $dtVal);
        @endphp
        <input type="datetime-local" name="{{ $name }}" value="{{ $dtVal }}" class="layui-input" step="1">
        @break
    @case('select')
        <select name="{{ $name }}" lay-ignore {{ $field->is_required ? 'required' : '' }}>
            <option value="">请选择</option>
            @foreach($opts as $k => $v)
            <option value="{{ $k }}" {{ (string)$value === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
        @break
    @case('radio')
        <div style="display:flex;gap:15px;flex-wrap:wrap;">
            @foreach($opts as $k => $v)
            <input type="radio" name="{{ $name }}" value="{{ $k }}" title="{{ $v }}" {{ (string)$value === (string)$k ? 'checked' : '' }}>
            @endforeach
        </div>
        @break
    @case('checkbox')
        <div style="display:flex;gap:15px;flex-wrap:wrap;">
            @foreach($opts as $k => $v)
            <input type="checkbox" name="{{ $name }}[]" value="{{ $k }}" lay-skin="primary" title="{{ $v }}"
                {{ is_array($value) && in_array($k, $value) ? 'checked' : (is_string($value) && in_array($k, json_decode($value, true) ?? []) ? 'checked' : '') }}>
            @endforeach
        </div>
        @break
    @case('editor')
        <textarea name="{{ $name }}" id="ckeditor-{{ $name }}" class="ckeditor-field" style="min-height:200px">{{ $value }}</textarea>
        @break
    @case('file')
        <div class="file-upload-wrap" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;" data-name="{{ $name }}">
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="file-path-input" {{ $field->is_required ? 'required' : '' }}>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-primary layui-btn-fluid" style="width:auto;">
                <i class="layui-icon layui-icon-upload"></i> 选择文件
                <input type="file" class="file-upload-input" accept="*/*" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;">
            </button>
            <span class="file-path-display" style="color:#999;font-size:12px;">{!! $value ? '<a href="'.asset($value).'" target="_blank" rel="noopener">'.$value.'</a>' : '未上传' !!}</span>
            <a href="javascript:;" class="file-clear-link layui-btn layui-btn-sm layui-btn-primary" style="{{ $value ? '' : 'display:none' }}"><i class="layui-icon layui-icon-close"></i> 清除</a>
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
            <input type="text" class="layui-input relation-input" placeholder="输入搜索或选择" value="{{ $initLabel }}" autocomplete="off">
            <div class="relation-dropdown" style="position:absolute;top:100%;left:0;right:0;max-height:200px;overflow-y:auto;z-index:9999;display:none;margin-top:2px;border:1px solid #e6e6e6;border-radius:2px;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.1);"></div>
        </div>
        @break
    @default
        <input type="text" name="{{ $name }}" value="{{ is_array($value) ? json_encode($value) : $value }}" class="layui-input" placeholder="{{ $field->label }}">
@endswitch
