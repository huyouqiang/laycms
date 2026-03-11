@extends('layouts.app')

@section('title', '用户管理')

@section('content')
<div class="layui-card">
    <div class="layui-card-header">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAdd"><i class="layui-icon layui-icon-add-1"></i> 新建用户</button>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
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
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary edit-user" data-user='@json($u)'><i class="layui-icon layui-icon-edit"></i> 编辑</button>
                        <form action="{{ route('users.destroy', $u) }}" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-danger"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                        @else
                        <span class="layui-badge layui-bg-gray">不可操作</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="userModalBox" style="display:none;">
    <form class="layui-form" id="userForm" style="padding:20px;">
        <input type="hidden" name="user_id" id="userId">
        <div class="layui-form-item" id="usernameWrap">
            <label class="layui-form-label layui-form-required">用户名</label>
            <div class="layui-input-block">
                <input type="text" name="username" id="username" class="layui-input" required>
            </div>
        </div>
        <div class="layui-form-item" id="passwordWrap">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" id="password" class="layui-input" placeholder="编辑时留空则不修改">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">昵称</label>
            <div class="layui-input-block">
                <input type="text" name="nickname" id="nickname" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">用户组</label>
            <div class="layui-input-block">
                <select name="user_group_id" id="userGroupId" required>
                    @foreach($groups as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="checkbox" name="is_active" id="isActive" lay-skin="switch" lay-text="正常|禁用" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal">保存</button>
                <button type="button" class="layui-btn layui-btn-primary" id="userModalClose">取消</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
layui.use(['jquery', 'layer', 'form'], function(){
    var $ = layui.$;
    var layer = layui.layer;
    var form = layui.form;
    var userModalIndex = 0;

    function openUserModal(title, editData){
        var html = $('#userModalBox').html();
        userModalIndex = layer.open({
            type: 1,
            title: title,
            area: ['450px', '480px'],
            content: html,
            success: function(layero, index){
                form.render('select');
                form.render('checkbox');
                if (editData) {
                    layero.find('#userId').val(editData.id);
                    layero.find('#username').val(editData.username).prop('readonly', true);
                    layero.find('#usernameWrap').show();
                    layero.find('#password').val('').prop('required', false);
                    layero.find('#passwordWrap').show();
                    layero.find('#nickname').val(editData.nickname || '');
                    layero.find('#userGroupId').val(editData.user_group_id || '');
                    layero.find('#isActive').prop('checked', editData.is_active !== false);
                } else {
                    layero.find('#userId').val('');
                    layero.find('#username').val('').prop('readonly', false);
                    layero.find('#usernameWrap').show();
                    layero.find('#password').val('').prop('required', true);
                    layero.find('#passwordWrap').show();
                    layero.find('#isActive').prop('checked', true);
                }
                form.render('checkbox');
                layero.find('#userModalClose').on('click', function(){ layer.close(index); });
                layero.find('#userForm').on('submit', function(e){
                    e.preventDefault();
                    var uid = layero.find('#userId').val();
                    var data = { _token: '{{ csrf_token() }}', _method: uid ? 'PUT' : 'POST' };
                    layero.find('#userForm').serializeArray().forEach(function(i){ if(i.name !== 'password' || i.value) data[i.name] = i.value; });
                    $.post(uid ? '/users/'+uid : '{{ route("users.store") }}', data).done(function(){ layer.close(index); location.reload(); }).fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.msg ? x.responseJSON.msg : (x.responseJSON && x.responseJSON.errors ? JSON.stringify(x.responseJSON.errors) : '保存失败')); });
                });
            }
        });
    }

    $('#btnAdd').on('click', function(){ openUserModal('新建用户', null); });
    $(document).on('click', '.edit-user', function(){ openUserModal('编辑用户', $(this).data('user')); });
});
</script>
@endpush
