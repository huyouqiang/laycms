<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LayCMS') - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">LayCMS</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">首页</a>
                @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">表单管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('form-groups.index') }}">表单分组</a></li>
                        <li><a class="dropdown-item" href="{{ route('forms.index') }}">表单列表</a></li>
                    </ul>
                </li>
                @endif
                @if($cms_user->is_root || $cms_user->hasPermission('_users', 'read'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">用户管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('user-groups.index') }}">用户组</a></li>
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">用户列表</a></li>
                    </ul>
                </li>
                @endif
            </div>
            <div class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ $cms_user->nickname ?? $cms_user->username }}</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('logout') }}">退出登录</a></li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>
    @php $currentTable = request()->route('tableName') ?? ''; @endphp
    <div class="d-flex sidebar-wrapper">
        <aside class="admin-sidebar" id="adminSidebar" data-current-table="{{ $currentTable }}">
            <nav class="nav flex-column pt-2 sidebar-nav">
                @foreach($menu_form_groups ?? [] as $grp)
                @php $hasCurrent = $grp->forms->contains(fn($f) => $f->table_name === $currentTable); @endphp
                <div class="sidebar-group{{ $hasCurrent ? ' expanded' : '' }}" data-group-id="{{ $grp->id }}">
                    <a class="nav-link sidebar-group-toggle" href="javascript:;" title="{{ $grp->name }}">
                        <span class="sidebar-text">{{ $grp->name }}</span>
                        <span class="sidebar-first" data-first="{{ mb_substr($grp->name, 0, 1) }}"></span>
                        <svg class="sidebar-group-arrow ms-auto" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="sidebar-group-items{{ $hasCurrent ? ' show' : '' }}">
                        @foreach($grp->forms ?? [] as $f)
                        @if($cms_user->is_root || $cms_user->canAccessTable($f->table_name, 'read'))
                        <a class="nav-link sidebar-item{{ (request()->route('tableName') ?? '') === $f->table_name ? ' active' : '' }}" href="{{ route('table-data.index', $f->table_name) }}" title="{{ $f->name }}"><span class="sidebar-text">{{ $f->name }}</span><span class="sidebar-first" data-first="{{ mb_substr($f->name, 0, 1) }}"></span></a>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </nav>
        </aside>
        <main class="flex-grow-1">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">@foreach($errors->all() as $e) {{ $e }} @endforeach</div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('modals')
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
    @stack('scripts')
</body>
</html>
