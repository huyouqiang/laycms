@extends('layouts.app')

@section('title', '权限设置 - ' . $group->name)

@section('content')
<div class="layui-card">
    <div class="layui-card-header">{{ $group->name }} - 权限设置</div>
    <div class="layui-card-body">
        <form class="layui-form" action="{{ route('permissions.update', $group) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="layui-form-item">
                <div class="layui-input-block" style="width:100%;">
                    <table class="layui-table">
                        <thead>
                            <tr><th>资源</th><th>新增</th><th>查看</th><th>编辑</th><th>删除</th></tr>
                        </thead>
                        <tbody>
                            @foreach($tables as $tableName => $label)
                            @php $p = $perms->get($tableName); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td><input type="checkbox" name="permissions[{{ $tableName }}][create]" value="1" lay-skin="primary" {{ $p && $p->can_create ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="permissions[{{ $tableName }}][read]" value="1" lay-skin="primary" {{ $p && $p->can_read ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="permissions[{{ $tableName }}][update]" value="1" lay-skin="primary" {{ $p && $p->can_update ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="permissions[{{ $tableName }}][delete]" value="1" lay-skin="primary" {{ $p && $p->can_delete ? 'checked' : '' }}></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn layui-btn-normal"><i class="layui-icon layui-icon-ok"></i> 保存权限</button>
                    <a href="{{ route('user-groups.index') }}" class="layui-btn layui-btn-primary"><i class="layui-icon layui-icon-return"></i> 返回</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>layui.use('form', function(){ var form = layui.form; form.render('checkbox'); });</script>
@endpush
