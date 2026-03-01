@extends('layouts.app')

@section('title', '权限设置 - ' . $group->name)

@section('content')
<div class="card">
    <div class="card-header">{{ $group->name }} - 权限设置</div>
    <div class="card-body">
        <form action="{{ route('permissions.update', $group) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>资源</th><th>新增</th><th>查看</th><th>编辑</th><th>删除</th></tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $tableName => $label)
                        @php $p = $perms->get($tableName); @endphp
                        <tr>
                            <td>{{ $label }}</td>
                            <td><input type="checkbox" name="permissions[{{ $tableName }}][create]" value="1" class="form-check-input" {{ $p && $p->can_create ? 'checked' : '' }}></td>
                            <td><input type="checkbox" name="permissions[{{ $tableName }}][read]" value="1" class="form-check-input" {{ $p && $p->can_read ? 'checked' : '' }}></td>
                            <td><input type="checkbox" name="permissions[{{ $tableName }}][update]" value="1" class="form-check-input" {{ $p && $p->can_update ? 'checked' : '' }}></td>
                            <td><input type="checkbox" name="permissions[{{ $tableName }}][delete]" value="1" class="form-check-input" {{ $p && $p->can_delete ? 'checked' : '' }}></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>保存权限</button>
                <a href="{{ route('user-groups.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>返回</a>
            </div>
        </form>
    </div>
</div>
@endsection
