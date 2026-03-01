<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'LayCMS'); ?> - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-with-sidebar">
        <div class="navbar-brand-wrap">
            <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">LayCMS</a>
        </div>
        <div class="navbar-nav-wrap">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="<?php echo e(route('dashboard')); ?>">首页</a>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">表单管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('form-groups.index')); ?>">表单分组</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('forms.index')); ?>">表单列表</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if($cms_user->is_root || $cms_user->hasPermission('_users', 'read')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">用户管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('user-groups.index')); ?>">用户组</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('users.index')); ?>">用户列表</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </div>
            <div class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><?php echo e($cms_user->nickname ?? $cms_user->username); ?></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo e(route('logout')); ?>">退出登录</a></li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>
    <?php $currentTable = request()->route('tableName') ?? ''; ?>
    <div class="d-flex sidebar-wrapper">
        <aside class="admin-sidebar" id="adminSidebar" data-current-table="<?php echo e($currentTable); ?>">
            <nav class="nav flex-column pt-2 sidebar-nav">
                <?php $__currentLoopData = $menu_form_groups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $hasCurrent = $grp->forms->contains(fn($f) => $f->table_name === $currentTable); ?>
                <div class="sidebar-group<?php echo e($hasCurrent ? ' expanded' : ''); ?>" data-group-id="<?php echo e($grp->id); ?>">
                    <a class="nav-link sidebar-group-toggle" href="javascript:;" title="<?php echo e($grp->name); ?>">
                        <span class="sidebar-text"><?php echo e($grp->name); ?></span>
                        <span class="sidebar-first" data-first="<?php echo e(mb_substr($grp->name, 0, 1)); ?>"></span>
                        <svg class="sidebar-group-arrow ms-auto" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="sidebar-group-items<?php echo e($hasCurrent ? ' show' : ''); ?>">
                        <?php $__currentLoopData = $grp->forms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($cms_user->is_root || $cms_user->canAccessTable($f->table_name, 'read')): ?>
                        <a class="nav-link sidebar-item<?php echo e((request()->route('tableName') ?? '') === $f->table_name ? ' active' : ''); ?>" href="<?php echo e(route('table-data.index', $f->table_name)); ?>" title="<?php echo e($f->name); ?>"><span class="sidebar-text"><?php echo e($f->name); ?></span><span class="sidebar-first" data-first="<?php echo e(mb_substr($f->name, 0, 1)); ?>"></span></a>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </aside>
        <main class="flex-grow-1">
            <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($e); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <?php echo $__env->yieldPushContent('modals'); ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function(){
        var key = 'laycms_sidebar_groups';
        var stored = localStorage.getItem(key);
        var expanded;
        try { expanded = stored ? JSON.parse(stored) : null; } catch(e) { expanded = null; }
        if (expanded === null) {
            expanded = []; document.querySelectorAll('.sidebar-group[data-group-id]').forEach(function(g){ expanded.push(g.dataset.groupId); });
        }
        document.querySelectorAll('.sidebar-group.expanded[data-group-id]').forEach(function(g){ if (expanded.indexOf(g.dataset.groupId) < 0) expanded.push(g.dataset.groupId); });
        if (!stored) localStorage.setItem(key, JSON.stringify(expanded));
        function save(){ localStorage.setItem(key, JSON.stringify(expanded)); }
        document.querySelectorAll('.sidebar-group-toggle').forEach(function(btn){
            var group = btn.closest('.sidebar-group');
            var id = group?.dataset?.groupId;
            if (!id) return;
            var items = group?.querySelector('.sidebar-group-items');
            var isExp = expanded.indexOf(id) >= 0;
            if (items) items.classList.toggle('show', isExp);
            if (group) group.classList.toggle('expanded', isExp);
            btn.addEventListener('click', function(e){ e.preventDefault();
                var i = expanded.indexOf(id);
                if (i >= 0) expanded.splice(i,1); else expanded.push(id);
                isExp = expanded.indexOf(id) >= 0;
                if (items) items.classList.toggle('show', isExp);
                if (group) group.classList.toggle('expanded', isExp);
                save();
            });
        });
    })();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /opt/homebrew/var/www/laycms/resources/views/layouts/app.blade.php ENDPATH**/ ?>