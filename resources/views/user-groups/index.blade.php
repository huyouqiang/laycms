@extends('layouts.app')

@section('title', '用户组管理')

@section('content')
<div class="layui-card">
    <div class="layui-card-header">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnAdd"><i class="layui-icon layui-icon-add-1"></i> 新建用户组</button>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-table">
            <thead><tr><th>ID</th><th>名称</th><th>描述</th><th>用户数</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($groups as $g)
                <tr>
                    <td>{{ $g->id }}</td>
                    <td>{{ $g->name }}</td>
                    <td>{{ $g->description ?? '-' }}</td>
                    <td>{{ $g->users_count }}</td>
                    <td>
                        <a href="{{ route('permissions.edit', $g) }}" class="layui-btn layui-btn-xs layui-btn-primary"><i class="layui-icon layui-icon-password"></i> 权限</a>
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-normal edit-group" data-group='@json($g)'><i class="layui-icon layui-icon-edit"></i> 编辑</button>
                        <form action="{{ route('user-groups.destroy', $g) }}" method="POST" style="display:inline;" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="layui-btn layui-btn-xs layui-btn-danger"><i class="layui-icon layui-icon-delete"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="groupModalBox" style="display:none;">
    <form class="layui-form" id="groupForm" style="padding:20px;">
        <input type="hidden" name="group_id" id="groupId">
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" id="groupName" class="layui-input" required>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <input type="text" name="description" id="groupDesc" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal">保存</button>
                <button type="button" class="layui-btn layui-btn-primary" id="groupModalClose">取消</button>
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
    var groupModalIndex = 0;

    function openGroupModal(title, editData){
        var html = $('#groupModalBox').html();
        groupModalIndex = layer.open({
            type: 1,
            title: title,
            area: ['420px', '300px'],
            content: html,
            success: function(layero, index){
                if (editData) {
                    layero.find('#groupId').val(editData.id);
                    layero.find('#groupName').val(editData.name);
                    layero.find('#groupDesc').val(editData.description || '');
                } else {
                    layero.find('#groupId').val('');
                    layero.find('#groupForm')[0].reset();
                }
                layero.find('#groupModalClose').on('click', function(){ layer.close(index); });
                layero.find('#groupForm').on('submit', function(e){
                    e.preventDefault();
                    var gid = layero.find('#groupId').val();
                    var data = { _token: '{{ csrf_token() }}', _method: gid ? 'PUT' : 'POST', name: layero.find('#groupName').val(), description: layero.find('#groupDesc').val() };
                    $.post(gid ? '/user-groups/'+gid : '{{ route("user-groups.store") }}', data).done(function(){ layer.close(index); location.reload(); }).fail(function(x){ layer.msg(x.responseJSON && x.responseJSON.msg ? x.responseJSON.msg : '保存失败'); });
                });
            }
        });
    }

    $('#btnAdd').on('click', function(){ openGroupModal('新建用户组', null); });
    $(document).on('click', '.edit-group', function(){ openGroupModal('编辑用户组', $(this).data('group')); });
});
</script>
@endpush
