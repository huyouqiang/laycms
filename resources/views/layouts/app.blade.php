<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LayCMS') - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-with-sidebar">
        <div class="navbar-brand-wrap">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}"><i class="bi bi-grid-3x3-gap"></i> LayCMS</a>
        </div>
        <div class="navbar-nav-wrap">
            <div class="navbar-nav me-auto">
                <a class="nav-link d-flex align-items-center gap-1" href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> 首页</a>
                @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown"><i class="bi bi-ui-checks"></i> 表单管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('form-groups.index') }}"><i class="bi bi-folder2"></i> 表单分组</a></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('forms.index') }}"><i class="bi bi-file-earmark-text"></i> 表单列表</a></li>
                    </ul>
                </li>
                @endif
                @if($cms_user->is_root || $cms_user->hasPermission('_users', 'read'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown"><i class="bi bi-people"></i> 用户管理</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user-groups.index') }}"><i class="bi bi-person-badge"></i> 用户组</a></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('users.index') }}"><i class="bi bi-person-lines-fill"></i> 用户列表</a></li>
                    </ul>
                </li>
                @endif
            </div>
            <div class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> {{ $cms_user->nickname ?? $cms_user->username }}</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 text-muted small">
                            @if($cms_user->is_root)
                                角色：超级管理员
                            @elseif($cms_user->userGroup)
                                角色：{{ $cms_user->userGroup->name }}
                            @else
                                角色：-
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right"></i> 退出登录</a></li>
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
                    <a class="nav-link sidebar-group-toggle d-flex align-items-center" href="javascript:;" title="{{ $grp->name }}">
                        <i class="bi bi-folder2-open me-2 sidebar-group-icon"></i>
                        <span class="sidebar-text">{{ $grp->name }}</span>
                        <span class="sidebar-first" data-first="{{ mb_substr($grp->name, 0, 1) }}"></span>
                        <svg class="sidebar-group-arrow ms-auto" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </a>
                    <div class="sidebar-group-items{{ $hasCurrent ? ' show' : '' }}">
                        @foreach($grp->forms ?? [] as $f)
                        @if($cms_user->is_root || $cms_user->canAccessTable($f->table_name, 'read'))
                        <a class="nav-link sidebar-item d-flex align-items-center{{ (request()->route('tableName') ?? '') === $f->table_name ? ' active' : '' }}" href="{{ route('table-data.index', $f->table_name) }}" title="{{ $f->name }}"><i class="bi bi-table me-2 sidebar-item-icon"></i><span class="sidebar-text">{{ $f->name }}</span><span class="sidebar-first" data-first="{{ mb_substr($f->name, 0, 1) }}"></span></a>
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
