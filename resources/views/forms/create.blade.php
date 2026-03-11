@extends('layouts.app')

@section('title', '新建表单')

@section('content')
<div class="layui-card">
    <div class="layui-card-header">新建表单</div>
    <div class="layui-card-body">
        <form class="layui-form" action="{{ route('forms.store') }}" method="POST" style="max-width:600px;">
            @csrf
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单分组</label>
                <div class="layui-input-block">
                    <select name="form_group_id" required>
                        <option value="">请选择分组</option>
                        @foreach($formGroups ?? [] as $g)
                        <option value="{{ $g->id }}" {{ old('form_group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">表单名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" value="{{ old('name') }}" placeholder="如：文章" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label layui-form-required">数据表名</label>
                <div class="layui-input-block">
                    <input type="text" name="table_name" class="layui-input" value="{{ old('table_name') }}" placeholder="如：articles（小写字母数字下划线）" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" name="description" class="layui-input" value="{{ old('description') }}" placeholder="可选">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-add-circle"></i> 创建</button>
                    <a href="{{ route('forms.index') }}" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>layui.use('form', function(){ var form = layui.form; form.render('select'); });</script>
@endpush
