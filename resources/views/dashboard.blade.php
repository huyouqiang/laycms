@extends('layouts.app')

@section('title', '首页')

@section('content')
<div class="layui-card">
    <div class="layui-card-body">
        <blockquote class="layui-elem-quote layui-quote-nm layui-bg-cms">欢迎使用 LayCMS 动态表单管理系统</blockquote>
    </div>
</div>
<div class="layui-row layui-col-space15">
    @foreach($forms as $form)
    <div class="layui-col-md4">
        <div class="layui-card">
            <div class="layui-card-header">{{ $form->name }}</div>
            <div class="layui-card-body">
                <p style="color:#666;margin-bottom:15px;">{{ $form->description ?? '暂无描述' }}</p>
                <a href="{{ route('table-data.index', $form->table_name) }}" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-table"></i> 管理数据</a>
                @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read'))
                <a href="{{ route('form-fields.index', $form) }}" class="layui-btn layui-btn-sm layui-btn-primary"><i class="layui-icon layui-icon-set"></i> 字段配置</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
