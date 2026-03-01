@extends('layouts.app')

@section('title', '用户组管理')

@section('content')
<div class="card">
    <div class="card-header">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal" id="btnAdd">新建用户组</button>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>ID</th><th>名称</th><th>描述</th><th>用户数</th><th>操作</th></tr></thead>
            <tbody>
                @foreach($groups as $g)
                <tr>
                    <td>{{ $g->id }}</td>
                    <td>{{ $g->name }}</td>
                    <td>{{ $g->description ?? '-' }}</td>
                    <td>{{ $g->users_count }}</td>
                    <td>
                        <a href="{{ route('permissions.edit', $g) }}" class="btn btn-sm btn-outline-primary">权限</a>
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-group" data-group='@json($g)'>编辑</button>
                        <form action="{{ route('user-groups.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('确定删除？');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="groupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalTitle">新建用户组</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="groupForm">
                <div class="modal-body">
                    <input type="hidden" name="group_id" id="groupId">
                    <div class="mb-3">
                        <label class="form-label">名称</label>
                        <input type="text" name="name" id="groupName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <input type="text" name="description" id="groupDesc" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
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
        $('#groupModalTitle').text('新建用户组');
        $('#groupId').val('');
        $('#groupForm')[0].reset();
    });
    $('.edit-group').on('click', function(){
        var g = $(this).data('group');
        $('#groupModalTitle').text('编辑用户组');
        $('#groupId').val(g.id);
        $('#groupName').val(g.name);
        $('#groupDesc').val(g.description || '');
        new bootstrap.Modal(document.getElementById('groupModal')).show();
    });
    $('#groupForm').on('submit', function(e){
        e.preventDefault();
        var gid = $('#groupId').val();
        var data = { _token: '{{ csrf_token() }}', _method: gid ? 'PUT' : 'POST', name: $('#groupName').val(), description: $('#groupDesc').val() };
        $.post(gid ? '/user-groups/'+gid : '{{ route("user-groups.store") }}', data).done(function(){ location.reload(); }).fail(function(x){ alert(x.responseJSON?.msg || '保存失败'); });
    });
});
</script>
@endpush
