@extends('layouts.app')

@section('title', '表单管理')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>表单列表</span>
        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'create'))
        <a href="{{ route('forms.create') }}" class="btn btn-sm btn-primary">新建表单</a>
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>ID</th><th>表单分组</th><th>表单名称</th><th>数据表</th><th>字段数</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($forms as $f)
                <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->formGroup?->name ?? '-' }}</td>
                    <td>{{ $f->name }}</td>
                    <td>{{ $f->table_name }}</td>
                    <td>{{ $f->fields_count }}</td>
                    <td>
                        <a href="{{ route('table-data.index', $f->table_name) }}" class="btn btn-sm btn-outline-primary">数据</a>
                        <a href="{{ route('form-fields.index', $f) }}" class="btn btn-sm btn-outline-secondary">字段</a>
                        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'update'))
                        <a href="{{ route('forms.edit', $f) }}" class="btn btn-sm btn-outline-secondary">编辑</a>
                        @endif
                        @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'delete'))
                        <form action="{{ route('forms.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
