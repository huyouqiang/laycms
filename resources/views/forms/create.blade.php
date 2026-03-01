@extends('layouts.app')

@section('title', '新建表单')

@section('content')
<div class="card">
    <div class="card-header">新建表单</div>
    <div class="card-body">
        <form action="{{ route('forms.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">表单分组</label>
                <select name="form_group_id" class="form-select" required>
                    <option value="">请选择分组</option>
                    @foreach($formGroups ?? [] as $g)
                    <option value="{{ $g->id }}" {{ old('form_group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">表单名称</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="如：文章" required>
            </div>
            <div class="mb-3">
                <label class="form-label">数据表名</label>
                <input type="text" name="table_name" class="form-control" value="{{ old('table_name') }}" placeholder="如：articles（小写字母数字下划线）" required>
            </div>
            <div class="mb-3">
                <label class="form-label">描述</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="可选">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>创建</button>
            <a href="{{ route('forms.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>返回</a>
        </form>
    </div>
</div>
@endsection
