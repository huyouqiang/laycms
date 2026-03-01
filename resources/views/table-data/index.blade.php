@extends('layouts.app')

@section('title', $form->name . ' - 数据管理')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>{{ $form->name }} 数据列表</span>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm" style="width:150px" placeholder="搜索">
            @if($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'create'))
            <a href="{{ route('table-data.create', $form->table_name) }}" class="btn btn-sm btn-primary">新增</a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-data-scroll">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        @foreach($form->fields->where('is_list_visible', true) as $f)
                        <th>{{ $f->label }}</th>
                        @endforeach
                        <th style="width:150px">操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <nav class="p-2" id="pagination"></nav>
    </div>
</div>
@endsection

@php
    $listFieldsData = $form->fields->where('is_list_visible', true)->map(function($f) {
        return ['name' => $f->field_name, 'control' => $f->form_control, 'opts' => $f->getOptionsArray()];
    })->values();
@endphp
@push('scripts')
<script>
$(function(){
    var tableName = '{{ $form->table_name }}';
    var url = '{{ route("table-data.index", $form->table_name) }}';
    var canEdit = {{ ($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'update')) ? 'true' : 'false' }};
    var canDel = {{ ($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'delete')) ? 'true' : 'false' }};
    var page = 1, limit = 50, total = 0;
    var listFields = @json($listFieldsData) || [];
    var baseUrl = '{{ url("/") }}'.replace(/\/$/, '');

    function formatCellVal(row, field) {
        var val = row[field.name];
        if (val === undefined || val === null || val === '') return { t: 'text', v: '-' };
        if (field.control === 'file' && val) {
            var href = baseUrl + '/' + String(val).replace(/^\//, '');
            var escaped = $('<div>').text(val).html();
            return { t: 'html', v: '<a href="' + href + '" target="_blank" rel="noopener">' + escaped + '</a>' };
        }
        if ((field.control === 'radio' || field.control === 'select') && field.opts && typeof field.opts === 'object') {
            var k = String(val);
            return { t: 'text', v: field.opts[k] !== undefined ? field.opts[k] : val };
        }
        if (field.control === 'checkbox' && field.opts && typeof field.opts === 'object') {
            var arr;
            if (typeof val === 'string') {
                try { arr = JSON.parse(val); } catch(e) { arr = [val]; }
                arr = Array.isArray(arr) ? arr : [val];
            } else {
                arr = Array.isArray(val) ? val : [val];
            }
            var txt = arr.map(function(k){ return field.opts[String(k)] !== undefined ? field.opts[String(k)] : k; }).join('/') || val;
            return { t: 'text', v: txt };
        }
        if (field.control === 'editor' && val) {
            var div = document.createElement('div');
            div.innerHTML = String(val);
            var text = (div.textContent || div.innerText || '').replace(/\s+/g, ' ').trim();
            var maxLen = 20;
            return { t: 'text', v: text.length > maxLen ? text.slice(0, maxLen) + '...' : text };
        }
        return { t: 'text', v: val };
    }

    function loadData(){
        $.get(url, { page: page, limit: limit, search: $('#searchInput').val() }, function(res){
            var tbody = $('#dataTable tbody').empty();
            (res.data || []).forEach(function(row, i){
                var tr = $('<tr></tr>');
                tr.append($('<td></td>').text((page-1)*limit + i + 1));
                tr.append($('<td></td>').text(row.id));
                listFields.forEach(function(f){
                    var r = formatCellVal(row, f);
                    tr.append($('<td></td>')[r.t === 'html' ? 'html' : 'text'](r.v));
                });
                var actions = $('<td></td>');
                if(canEdit) actions.append($('<a class="btn btn-sm btn-outline-primary me-1"></a>').text('编辑').attr('href', url+'/'+row.id+'/edit'));
                if(canDel) actions.append($('<button class="btn btn-sm btn-outline-danger"></button>').text('删除').on('click', function(){ if(confirm('确定删除？')) $.post(url+'/'+row.id, {_token:'{{ csrf_token() }}',_method:'DELETE'}, function(){ loadData(); }); }));
                tr.append(actions);
                tbody.append(tr);
            });
            total = res.count || 0;
            renderPagination();
        });
    }

    function renderPagination(){
        var pages = Math.ceil(total / limit) || 1;
        var maxBtns = 10;
        var half = Math.floor(maxBtns / 2);
        var start = Math.max(1, page - half);
        var end = Math.min(pages, start + maxBtns - 1);
        if (end - start + 1 < maxBtns) start = Math.max(1, end - maxBtns + 1);

        var html = '<ul class="pagination pagination-sm mb-0 flex-wrap">';
        html += '<li class="page-item'+(page<=1?' disabled':'')+'"><a class="page-link" href="#" data-page="1">首页</a></li>';
        html += '<li class="page-item'+(page<=1?' disabled':'')+'"><a class="page-link" href="#" data-page="'+(page-1)+'">上一页</a></li>';
        for(var i=start;i<=end;i++){
            html += '<li class="page-item'+(i===page?' active':'')+'"><a class="page-link" href="#" data-page="'+i+'">'+i+'</a></li>';
        }
        html += '<li class="page-item'+(page>=pages?' disabled':'')+'"><a class="page-link" href="#" data-page="'+(page+1)+'">下一页</a></li>';
        html += '<li class="page-item'+(page>=pages?' disabled':'')+'"><a class="page-link" href="#" data-page="'+pages+'">尾页</a></li>';
        html += '</ul>';
        $('#pagination').html(html).find('.page-link').on('click', function(e){
            e.preventDefault();
            if($(this).closest('.page-item').hasClass('disabled')) return;
            var p = parseInt($(this).data('page'), 10);
            if(p>=1 && p<=pages){ page=p; loadData(); }
        });
    }

    $('#searchInput').on('keypress', function(e){ if(e.which===13){ page=1; loadData(); } });
    loadData();
});
</script>
@endpush
