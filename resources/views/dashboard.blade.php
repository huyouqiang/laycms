@extends('layouts.app')

@section('title', '首页')

@section('content')
<div class="alert alert-info">欢迎使用 LayCMS 动态表单管理系统</div>
<div class="row g-3">
    @foreach($forms as $form)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">{{ $form->name }}</div>
            <div class="card-body">
                <p class="card-text">{{ $form->description ?? '暂无描述' }}</p>
                <a href="{{ route('table-data.index', $form->table_name) }}" class="btn btn-sm btn-primary"><i class="bi bi-table me-1"></i>管理数据</a>
                @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read'))
                <a href="{{ route('form-fields.index', $form) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-sliders me-1"></i>字段配置</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
