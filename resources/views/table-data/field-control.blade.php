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
        <textarea name="{{ $name }}" class="form-control" style="min-height:200px">{{ $value }}</textarea>
        @break
    @case('relation')
        @php
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
        @endphp
        <select name="{{ $name }}" class="form-select" {{ $field->is_required ? 'required' : '' }}>
            <option value="">请选择</option>
            @foreach($relOpts as $k => $v)
            <option value="{{ $k }}" {{ (string)$value === (string)$k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
        @break
    @default
        <input type="text" name="{{ $name }}" value="{{ is_array($value) ? json_encode($value) : $value }}" class="form-control" placeholder="{{ $field->label }}">
@endswitch
