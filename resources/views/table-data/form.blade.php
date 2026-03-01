@extends('layouts.app')

@section('title', ($row ? '编辑' : '新增') . ' - ' . $form->name)

@section('content')
<div class="card">
    <div class="card-header">{{ $row ? '编辑' : '新增' }} {{ $form->name }}</div>
    <div class="card-body">
        <form action="{{ $row ? route('table-data.update', [$tableName, $row->id]) : route('table-data.store', $tableName) }}" method="POST">
            @csrf
            @if($row) @method('PUT') @endif
            @foreach($form->fields as $field)
            <div class="mb-3">
                <label class="form-label{{ $field->is_required ? ' required' : '' }}">{{ $field->label }}
                @if($field->form_control === 'relation')
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="ms-1 text-muted" title="外键关联">
                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/>
                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/>
                </svg>
                @endif
                </label>
                @include('table-data.field-control', ['field' => $field, 'value' => $row?->{$field->field_name} ?? old($field->field_name) ?? ''])
            </div>
            @endforeach
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="{{ route('table-data.index', $tableName) }}" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if($form->fields->contains('form_control', 'editor'))
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full/lang/zh-cn.js"></script>
@endif
<script>
$(function(){
    @if($form->fields->contains('form_control', 'editor'))
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
    @endif
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
            $.get('{{ route("table-data.relation-options") }}', { table: table, ref: ref, display: display, q: q || '' }, function(res){
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
        fd.append('_token', '{{ csrf_token() }}');
        $display.text('上传中...');
        $.ajax({
            url: '{{ route("upload.store") }}',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function(res){
            if (res.code === 0) {
                $pathInput.val(res.path);
                var baseUrl = '{{ url("/") }}'.replace(/\/$/, '');
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
@endpush
