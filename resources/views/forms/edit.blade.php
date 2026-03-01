@extends('layouts.app')

@section('title', '编辑表单')

@section('content')
<div class="card">
    <div class="card-header">编辑表单</div>
    <div class="card-body">
        <form action="{{ route('forms.update', $form) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">表单分组</label>
                <select name="form_group_id" class="form-select" required>
                    @foreach($formGroups ?? [] as $g)
                    <option value="{{ $g->id }}" {{ old('form_group_id', $form->form_group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">表单名称</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $form->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">数据表名</label>
                <input type="text" name="table_name" class="form-control" value="{{ old('table_name', $form->table_name) }}" readonly style="background:#f5f5f5">
            </div>
            <div class="mb-3">
                <label class="form-label">描述</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $form->description) }}">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="{{ route('forms.index') }}" class="btn btn-secondary">返回</a>
            <a href="{{ route('form-fields.index', $form) }}" class="btn btn-outline-primary">配置字段</a>
        </form>
    </div>
</div>

@if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>关联表单（MySQL 外键）</span>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#relationModal" id="btnAddRelation">添加关联</button>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>本表字段</th><th>关联表单</th><th>关联字段</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($form->relations ?? [] as $rel)
                <tr>
                    <td>{{ $rel->formField?->field_name }}</td>
                    <td>{{ $rel->relatedForm?->name }}（{{ $rel->relatedForm?->table_name }}）</td>
                    <td>{{ $rel->related_field_name }}</td>
                    <td>
                        <form action="{{ route('form-relations.destroy', $rel) }}" method="POST" class="d-inline" onsubmit="return confirm('确定删除此关联？将移除数据库外键约束。');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if(($form->relations ?? collect())->isEmpty())
                <tr><td colspan="4" class="text-secondary">暂无关联</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="relationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">添加关联</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="relationForm">
                <div class="modal-body">
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <div class="mb-3">
                        <label class="form-label">本表字段</label>
                        <div class="d-flex gap-2">
                            <select name="form_field_id" id="formFieldId" class="form-select flex-grow-1">
                                <option value="">-- 新建字段 --</option>
                                @foreach($form->fields as $f)
                                @if(!$f->relation)
                                <option value="{{ $f->id }}" data-name="{{ $f->field_name }}">{{ $f->label }}（{{ $f->field_name }}）</option>
                                @endif
                                @endforeach
                            </select>
                            <input type="text" name="field_name" id="fieldName" class="form-control" placeholder="新字段名（如 category_id）" style="max-width:160px">
                        </div>
                        <small class="text-secondary">选择已有字段或输入新字段名</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">关联表单</label>
                        <select name="related_form_id" id="relatedFormId" class="form-select" required>
                            <option value="">请选择</option>
                            @foreach($otherForms ?? [] as $of)
                            <option value="{{ $of->id }}" data-table="{{ $of->table_name }}">{{ $of->name }}（{{ $of->table_name }}）</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">关联字段</label>
                        <select name="related_field_name" id="relatedFieldName" class="form-select" required>
                            <option value="id">id</option>
                        </select>
                        <small class="text-secondary">选择关联表中的字段（通常为 id）</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
@push('scripts')
<script>
$(function(){
    $('#relatedFormId').on('change', function(){
        var fid = $(this).val();
        var sel = $('#relatedFieldName').empty().append('<option value="id">id</option>');
        if (!fid) return;
        var url = '{{ url("/forms") }}/' + fid + '/related-columns';
        $.get(url, function(res){
            (res.columns || []).forEach(function(col){
                if (col !== 'id') sel.append($('<option></option>').val(col).text(col));
            });
        });
    });
    $('#formFieldId').on('change', function(){
        var v = $(this).val();
        $('#fieldName').prop('disabled', !!v).val(v ? $(this).find('option:selected').data('name') : '');
    });
    $('#relationForm').on('submit', function(e){
        e.preventDefault();
        var fd = new FormData(this);
        if (!fd.get('form_field_id') && !fd.get('field_name')) { alert('请选择已有字段或输入新字段名'); return; }
        if (!fd.get('related_form_id')) { alert('请选择关联表单'); return; }
        var data = { _token: '{{ csrf_token() }}', form_id: fd.get('form_id'), related_form_id: fd.get('related_form_id'), related_field_name: fd.get('related_field_name') || 'id' };
        if (fd.get('form_field_id')) data.form_field_id = fd.get('form_field_id'); else data.field_name = fd.get('field_name');
        $.post('{{ route("form-relations.store") }}', data).done(function(){ location.reload(); }).fail(function(x){ alert(x.responseJSON?.message || (x.responseJSON?.errors ? JSON.stringify(x.responseJSON.errors) : '添加失败')); });
    });
});
</script>
@endpush
@endif
