@extends('layouts.app')

@section('title', '表单管理')

@section('content')
<div class="layui-card">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <span>表单列表</span>
        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'create'))
        <a href="{{ route('forms.create') }}" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-add-1"></i> 新建表单</a>
        @endif
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table" lay-skin="line">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>表单分组</th>
                    <th>表单名称</th>
                    <th>数据表</th>
                    <th>字段数</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $f)
                <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->formGroup?->name ?? '-' }}</td>
                    <td>{{ $f->name }}</td>
                    <td>{{ $f->table_name }}</td>
                    <td>{{ $f->fields_count }}</td>
                    <td>
                        <a href="{{ route('table-data.index', $f->table_name) }}" class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon layui-icon-table"></i> 数据</a>
                        <a href="{{ route('form-fields.index', $f) }}" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-list"></i> 字段</a>
                        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
                        <a href="{{ route('forms.edit', $f) }}" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-edit"></i> 编辑</a>
                        @endif
                        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'delete'))
                        <form action="{{ route('forms.destroy', $f) }}" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
<script>
layui.use(['element'], function(){ layui.element.render('table'); });
</script>
@endpush
@endsection
