@extends('layouts.app')

@section('title', '用户管理')

@section('content')
<div class="card">
    <div class="card-header">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" id="btnAdd"><i class="bi bi-person-plus me-1"></i>新建用户</button>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>ID</th><th>用户名</th><th>昵称</th><th>用户组</th><th>根用户</th><th>状态</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->username }}</td>
                    <td>{{ $u->nickname ?? '-' }}</td>
                    <td>{{ $u->userGroup?->name ?? '-' }}</td>
                    <td>{{ $u->is_root ? '是' : '否' }}</td>
                    <td>{{ $u->is_active ? '正常' : '禁用' }}</td>
                    <td>
                        @if(!$u->is_root)
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-user" data-user='@json($u)'><i class="bi bi-pencil me-1"></i>编辑</button>
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>删除</button>
                        </form>
                        @else
                        <span class="badge bg-secondary">不可操作</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">新建用户</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="userId">
                    <div class="mb-3" id="usernameWrap">
                        <label class="form-label">用户名</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3" id="passwordWrap">
                        <label class="form-label">密码</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="编辑时留空则不修改">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">昵称</label>
                        <input type="text" name="nickname" id="nickname" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">用户组</label>
                        <select name="user_group_id" id="userGroupId" class="form-select" required>
                            @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="isActive" class="form-check-input" checked>
                            <label class="form-check-label" for="isActive">正常</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i>取消</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#btnAdd').on('click', function(){
        $('#userModalTitle').text('新建用户');
        $('#userId').val('');
        $('#username').val('').prop('readonly', false);
        $('#usernameWrap').show();
        $('#password').val('').prop('required', true);
        $('#passwordWrap').show();
        $('#userForm')[0].reset();
        $('#isActive').prop('checked', true);
    });
    $('.edit-user').on('click', function(){
        var u = $(this).data('user');
        $('#userModalTitle').text('编辑用户');
        $('#userId').val(u.id);
        $('#username').val(u.username).prop('readonly', true);
        $('#usernameWrap').show();
        $('#password').val('').prop('required', false);
        $('#passwordWrap').show();
        $('#nickname').val(u.nickname || '');
        $('#userGroupId').val(u.user_group_id || '');
        $('#isActive').prop('checked', u.is_active !== false);
        new bootstrap.Modal(document.getElementById('userModal')).show();
    });
    $('#userForm').on('submit', function(e){
        e.preventDefault();
        var uid = $('#userId').val();
        var data = { _token: '{{ csrf_token() }}', _method: uid ? 'PUT' : 'POST' };
        $(this).serializeArray().forEach(function(i){ if(i.name!='password'||i.value) data[i.name]=i.value; });
        $.post(uid ? '/users/'+uid : '{{ route("users.store") }}', data).done(function(){ location.reload(); }).fail(function(x){
            alert(x.responseJSON?.msg || (x.responseJSON?.errors ? JSON.stringify(x.responseJSON.errors) : '保存失败'));
        });
    });
});
</script>
@endpush
