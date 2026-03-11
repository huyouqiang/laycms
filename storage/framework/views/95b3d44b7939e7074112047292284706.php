<?php $__env->startSection('title', $form->name . ' - 数据管理'); ?>

<?php $__env->startSection('content'); ?>
<div class="layui-card table-data-card">
    <div class="layui-card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <span><i class="layui-icon layui-icon-table"></i> <?php echo e($form->name); ?></span>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <input type="text" id="searchInput" class="layui-input" style="width:160px;height:34px;display:inline-block;" placeholder="关键词搜索多字段">
            <?php $__currentLoopData = $form->fields->where('is_list_visible', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(in_array($f->form_control, ['input', 'textarea', 'number'])): ?>
            <input type="text" class="layui-input field-search" data-field="<?php echo e($f->field_name); ?>" placeholder="<?php echo e($f->label); ?>" style="width:120px;height:34px;display:inline-block;">
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="btnSearch"><i class="layui-icon layui-icon-search"></i> 搜索</button>
            <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="btnReset">重置</button>
            <?php if($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'create')): ?>
            <a href="<?php echo e(route('table-data.create', $form->table_name)); ?>" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-add-1"></i> 新增</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="layui-card-body" style="padding:0;">
        <table class="layui-hide" id="dataTable" lay-filter="dataTable"></table>
    </div>
</div>
<style>
.table-data-card .layui-card-body { overflow: visible; }
.layui-table-cell { line-height: 1.4; padding: 8px 10px; }
.field-search { margin-left: 0; }
/* 超宽时由 Layui 表格自带横向滚动折叠，不截断内容 */
.table-data-card .layui-table-body {
    overflow-x: auto;
}
.table-data-card .layui-table-body .layui-table-cell .layui-btn-group {
    display: inline-flex;
}
.table-data-card .layui-table-body .layui-table-cell .layui-btn-group .layui-btn {
    margin: 0;
}
.layui-nav{
    background-color: #001529;
}
</style>
<?php $__env->stopSection(); ?>

<?php
    $listFieldsData = $form->fields->where('is_list_visible', true)->map(function($f) {
        return ['name' => $f->field_name, 'label' => $f->label, 'control' => $f->form_control, 'opts' => $f->getOptionsArray()];
    })->values();
?>
<?php $__env->startPush('scripts'); ?>
<script>
layui.use(['table', 'layer', 'jquery'], function(){
    var table = layui.table;
    var layer = layui.layer;
    var $ = layui.$;

    var url = '<?php echo e(route("table-data.index", $form->table_name)); ?>';
    var baseUrl = '<?php echo e(url("/")); ?>'.replace(/\/$/, '');
    var canEdit = <?php echo e(($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'update')) ? 'true' : 'false'); ?>;
    var canDel = <?php echo e(($cms_user->is_root || $cms_user->canAccessTable($form->table_name, 'delete')) ? 'true' : 'false'); ?>;
    var listFields = <?php echo json_encode($listFieldsData, 15, 512) ?> || [];

    function getSearchWhere(){
        var where = {};
        var search = $('#searchInput').val();
        if (search) where.search = search;
        $('.field-search').each(function(){
            var val = $(this).val();
            if (val) where['search_' + $(this).data('field')] = val;
        });
        return where;
    }

    function formatCellVal(row, field) {
        var val = row[field.name];
        if (val === undefined || val === null || val === '') return { t: 'text', v: '-', title: '' };
        var plainText = '';
        if (field.control === 'file' && val) {
            plainText = String(val);
            var href = baseUrl + '/' + plainText.replace(/^\//, '');
            var escaped = plainText.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            return { t: 'html', v: '<a href="' + href + '" target="_blank" rel="noopener">' + escaped + '</a>', title: plainText };
        }
        if ((field.control === 'radio' || field.control === 'select') && field.opts && typeof field.opts === 'object') {
            var k = String(val);
            plainText = field.opts[k] !== undefined ? field.opts[k] : val;
            return { t: 'text', v: plainText, title: plainText };
        }
        if (field.control === 'checkbox' && field.opts && typeof field.opts === 'object') {
            var arr;
            if (typeof val === 'string') {
                try { arr = JSON.parse(val); } catch(e) { arr = [val]; }
                arr = Array.isArray(arr) ? arr : [val];
            } else {
                arr = Array.isArray(val) ? val : [val];
            }
            plainText = arr.map(function(k){ return field.opts[String(k)] !== undefined ? field.opts[String(k)] : k; }).join(' / ') || val;
            return { t: 'text', v: plainText, title: plainText };
        }
        if (field.control === 'editor' && val) {
            return { t: 'text', v: '[富文本]', title: '' };
        }
        plainText = String(val);
        return { t: 'text', v: plainText, title: plainText };
    }

    var COLS_WIDTH = 200;
    var cols = [
        { field: 'id', title: 'ID', width: 90, fixed: 'left', sort: false }
    ];
    listFields.forEach(function(f){
        cols.push({
            field: f.name,
            title: f.label,
            width: COLS_WIDTH,
            minWidth: COLS_WIDTH,
            sort: false,
            templet: function(d) {
                var r = formatCellVal(d, f);
                var display = r.t === 'html' ? r.v : (r.v || '-');
                return display;
            }
        });
    });
    cols.push({
        title: '操作',
        width: 170,
        fixed: 'right',
        sort: false,
        templet: function(d) {
            var h = '';
            if (canEdit || canDel) {
                h += '<div class="layui-btn-group">';
                if (canEdit) h += '<a class="layui-btn layui-btn-xs layui-btn-primary" href="' + url + '/' + d.id + '/edit"><i class="layui-icon layui-icon-edit"></i> 编辑</a>';
                if (canDel) h += '<button type="button" class="layui-btn layui-btn-xs layui-btn-primary layui-btn-delete" data-id="' + d.id + '"><i class="layui-icon layui-icon-delete"></i> 删除</button>';
                h += '</div>';
            } else h = '-';
            return h;
        }
    });

    var tableIns = table.render({
        elem: '#dataTable',
        id: 'dataTable',
        url: url,
        where: getSearchWhere(),
        method: 'get',
        page: true,
        limit: 50,
        limits: [15, 30, 50, 100],
        height: (function(){
            var el = document.querySelector('.table-data-card');
            var top = el ? el.getBoundingClientRect().top : 0;
            var reserve = 100;
            return Math.max(300, (window.innerHeight || document.documentElement.clientHeight) - top - reserve);
        })(),
        cols: [cols],
        parseData: function(res) {
            return { code: res.code || 0, msg: res.msg || '', count: res.count || 0, data: res.data || [] };
        },
        response: { statusCode: 0 },
        done: function() {
            var card = $('.table-data-card');
            card.off('click.tableDelete').on('click.tableDelete', '.layui-btn-delete', function() {
                var id = $(this).data('id');
                layer.confirm('确定删除该条数据？', {icon: 3}, function(idx) {
                    $.post(url + '/' + id, { _token: '<?php echo e(csrf_token()); ?>', _method: 'DELETE' })
                        .done(function() { table.reload('dataTable'); })
                        .fail(function(x) { layer.msg((x.responseJSON && x.responseJSON.msg) ? x.responseJSON.msg : '删除失败'); });
                    layer.close(idx);
                });
            });
        }
    });

    $('#searchInput').on('keypress', function(e) { if (e.which === 13) { table.reload('dataTable', { where: getSearchWhere(), page: { curr: 1 } }); } });
    $('#btnSearch').on('click', function() { table.reload('dataTable', { where: getSearchWhere(), page: { curr: 1 } }); });
    $('#btnReset').on('click', function() {
        $('#searchInput').val('');
        $('.field-search').val('');
        table.reload('dataTable', { where: {}, page: { curr: 1 } });
    });

    $(window).on('resize', function() {
        var el = document.querySelector('.table-data-card');
        var top = el ? el.getBoundingClientRect().top : 0;
        var reserve = 100;
        var h = Math.max(300, (window.innerHeight || document.documentElement.clientHeight) - top - reserve);
        table.reload('dataTable', { height: h });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/homebrew/var/www/laycms/resources/views/table-data/index.blade.php ENDPATH**/ ?>