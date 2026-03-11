@extends('layouts.app')

@section('title', ($row ? '编辑' : '新增') . ' - ' . $form->name)

@section('content')
<style>.layui-form-label-muted{color:#999;font-size:12px;font-weight:400;}</style>
<style>.layui-card-header-back:hover{color:#004080!important;text-decoration:underline;}</style>
<div class="layui-card">
    <div class="layui-card-header">
        @if($row)
        <i class="layui-icon layui-icon-edit"></i> {{ $form->name }}
        <a href="{{ route('table-data.index', $tableName) }}" class="layui-card-header-back" style="margin-left:12px;font-size:13px;color:#003366;">返回</a>
        @else
        新增 {{ $form->name }}
        <a href="{{ route('table-data.index', $tableName) }}" class="layui-card-header-back" style="margin-left:12px;font-size:13px;color:#003366;">返回</a>
        @endif
    </div>
    <div class="layui-card-body">
        <form class="layui-form" action="{{ $row ? route('table-data.update', [$tableName, $row->id]) : route('table-data.store', $tableName) }}" method="POST" style="max-width:800px;">
            @csrf
            @if($row) @method('PUT') @endif
            @foreach($form->fields as $field)
            <div class="layui-form-item">
                <label class="layui-form-label{{ $field->is_required ? ' layui-form-required' : '' }}">{{ $field->label }}<span class="layui-form-label-muted">（{{ $field->field_name }}）</span>
                @if($field->form_control === 'relation')
                <span class="layui-badge layui-bg-gray" title="外键关联">关联</span>
                @endif
                </label>
                <div class="layui-input-block">
                    @include('table-data.field-control', ['field' => $field, 'value' => $row?->{$field->field_name} ?? old($field->field_name) ?? ''])
                </div>
            </div>
            @endforeach
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-ok"></i> 保存</button>
                    <a href="{{ route('table-data.index', $tableName) }}" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                </div>
            </div>
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
layui.use(['jquery', 'form'], function(){
    var $ = layui.$;
    var form = layui.form;
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
                    var $a = $('<a href="javascript:;" class="layui-table-cell"></a>').css('display','block').text(item.label).data('value', item.value);
                    $a.on('click', function(){ $value.val($(this).data('value')); $input.val($(this).text()); $dropdown.hide(); });
                    $dropdown.append($a);
                });
                if ((res.data || []).length === 0) $dropdown.append('<div class="layui-table-cell" style="color:#999;">无匹配项</div>');
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
