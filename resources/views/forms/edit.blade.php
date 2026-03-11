@extends('layouts.app')

@section('title', '编辑表单')

@section('content')
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
        <form class="layui-form" action="{{ route('forms.update', $form) }}" method="POST" style="max-width:600px;">
            @csrf
            @method('PUT')
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单分组</label>
                <div class="layui-input-block">
                    <select name="form_group_id" required>
                        @foreach($formGroups ?? [] as $g)
                        <option value="{{ $g->id }}" {{ old('form_group_id', $form->form_group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" value="{{ old('name', $form->name) }}" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">数据表名</label>
                <div class="layui-input-block">
                    <input type="text" name="table_name" class="layui-input" value="{{ old('table_name', $form->table_name) }}" readonly style="background:#f5f5f5;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" name="description" class="layui-input" value="{{ old('description', $form->description) }}">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-ok"></i> 保存</button>
                    <a href="{{ route('forms.index') }}" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                    <a href="{{ route('form-fields.index', $form) }}" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-set"></i> 配置字段</a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
<div class="layui-card" style="margin-top:20px;">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span>关联表单（MySQL 外键）</span>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAddRelation"><i class="layui-icon layui-icon-link"></i> 添加关联</button>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
            <thead><tr><th>本表字段</th><th>关联表单</th><th>关联字段</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($form->relations ?? [] as $rel)
                <tr>
                    <td>{{ $rel->formField?->field_name }}</td>
                    <td>{{ $rel->relatedForm?->name }}（{{ $rel->relatedForm?->table_name }}）</td>
                    <td>{{ $rel->related_field_name }}</td>
                    <td>
                        <form action="{{ route('form-relations.destroy', $rel) }}" method="POST" style="display:inline;" onsubmit="return confirm('确定删除此关联？将移除数据库外键约束。');">
                            @csrf @method('DELETE')
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-danger"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if(($form->relations ?? collect())->isEmpty())
                <tr><td colspan="4" style="color:#999;">暂无关联</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="layui-card" style="margin-top:20px;">
    <div class="layui-card-header">添加索引</div>
    <div class="layui-card-body">
        @if(!empty($tableIndexes))
        <div style="margin-bottom:20px;">
            <label class="layui-form-label" style="width:auto;padding:0 10px 0 0;">已添加的索引</label>
            <table class="layui-table">
                <thead><tr><th>索引名称</th><th>对应字段</th><th>索引类型</th><th>操作</th></tr></thead>
                <tbody>
                    @foreach($tableIndexes as $idx)
                    <tr>
                        <td>{{ $idx['name'] }}</td>
                        <td>{{ $idx['column'] }}</td>
                        <td>{{ $idx['type'] }}</td>
                        <td>
                            @if($idx['name'] !== 'PRIMARY')
                            <form action="{{ route('forms.drop-index', $form) }}" method="POST" style="display:inline;" onsubmit="return confirm('确定删除索引 {{ $idx['name'] }}？');">
                                @csrf @method('DELETE')
                                <input type="hidden" name="index_name" value="{{ $idx['name'] }}">
                                <button type="submit" class="layui-btn layui-btn-xs layui-btn-danger"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                            </form>
                            @else
                            <span style="color:#999;">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @if(!empty($tableColumns))
        <form class="layui-form add-index-form" action="{{ route('forms.add-index', $form) }}" method="POST" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div class="layui-form-item" style="margin-bottom:0;min-width:200px;">
                <label class="layui-form-label">选择字段</label>
                <div class="layui-input-block" style="margin-left:100px;">
                    <select name="column_name" required>
                        <option value="">请选择要添加索引的字段</option>
                        @foreach($tableColumns as $col)
                        <option value="{{ $col }}">{{ $col }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="margin-bottom:0;">
                <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-add-circle"></i> 添加索引</button>
            </div>
        </form>
        <p style="color:#999;margin-top:10px;font-size:12px;">为数据表 {{ $form->table_name }} 的字段添加 MySQL 索引，以提升查询性能。</p>
        @else
        <p style="color:#999;margin:0;">数据表 {{ $form->table_name }} 暂无可用字段。</p>
        @endif
    </div>
</div>

<div id="relationModalBox" style="display:none;">
    <form class="layui-form" id="relationForm" style="padding:20px;">
        <input type="hidden" name="form_id" value="{{ $form->id }}">
        <div class="layui-form-item">
            <label class="layui-form-label">本表字段</label>
            <div class="layui-input-block" style="display:flex;gap:10px;">
                <select name="form_field_id" id="formFieldId" style="flex:1;">
                    <option value="">-- 新建字段 --</option>
                    @foreach($form->fields as $f)
                    @if(!$f->relation)
                    <option value="{{ $f->id }}" data-name="{{ $f->field_name }}">{{ $f->label }}（{{ $f->field_name }}）</option>
                    @endif
                    @endforeach
                </select>
                <input type="text" name="field_name" id="fieldName" class="layui-input" placeholder="新字段名（如 category_id）" style="max-width:160px;">
            </div>
            <div class="layui-form-mid layui-word-aux">选择已有字段或输入新字段名</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">关联表单</label>
            <div class="layui-input-block">
                <select name="related_form_id" id="relatedFormId" required>
                    <option value="">请选择</option>
                    @foreach($otherForms ?? [] as $of)
                    <option value="{{ $of->id }}" data-table="{{ $of->table_name }}">{{ $of->name }}（{{ $of->table_name }}）</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">关联字段</label>
            <div class="layui-input-block">
                <select name="related_field_name" id="relatedFieldName" required>
                    <option value="id">id</option>
                </select>
                <div class="layui-form-mid layui-word-aux">选择关联表中的字段（通常为 id）</div>
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
@endif
@endsection

@if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
@push('scripts')
<script>
layui.use(['jquery', 'layer', 'form'], function(){
    var $ = layui.$;
    var layer = layui.layer;
    var form = layui.form;
    form.render('select');

    $('#relatedFormId').on('change', function(){
        var fid = $(this).val();
        var sel = $('#relatedFieldName').empty().append('<option value="id">id</option>');
        if (!fid) return;
        var url = '{{ url("/forms") }}/' + fid + '/related-columns';
        $.get(url, function(res){
            (res.columns || []).forEach(function(col){
                if (col !== 'id') sel.append($('<option></option>').val(col).text(col));
            });
            form.render('select');
        });
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
                    var data = { _token: '{{ csrf_token() }}', form_id: fd.get('form_id'), related_form_id: fd.get('related_form_id'), related_field_name: fd.get('related_field_name') || 'id' };
                    if (fd.get('form_field_id')) data.form_field_id = fd.get('form_field_id'); else data.field_name = fd.get('field_name');
                    $.post('{{ route("form-relations.store") }}', data).done(function(){ layer.close(index); location.reload(); }).fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.message ? x.responseJSON.message : (x.responseJSON && x.responseJSON.errors ? JSON.stringify(x.responseJSON.errors) : '添加失败')); });
                });
            }
        });
    });
});
</script>
@endpush
@endif
