<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LayCMS') - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/css/layui.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<style>
.layui-nav{
    background-color: #001529;
}
.layui-laypage .layui-laypage-curr .layui-laypage-em{
    background-color: #001529;
}
.layui-form-radio:hover>*, .layui-form-radioed, .layui-form-radioed>i{
    color: #001529;
}
.layui-bg-green{
    background-color: #001529 !important;
}
.layui-nav-child{
    background-color: #001529;
    border-color: #001529;
}
.layui-layer-btn .layui-layer-btn0{
    background-color: #001529;
}
.layui-input:focus, .layui-textarea:focus{
    border-color: #001529 !important;
}
.layui-form-select dl dd.layui-this{
    color: #001529 !important;
}
.layui-form-onswitch{
    background-color: #001529;
    border-color: #001529;
}
</style>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header layui-bg-black">
            <div class="layui-logo" style="width:210px;left:0;text-align:center;">
                <a href="{{ route('dashboard') }}" style="color:#fff;font-size:18px;font-weight:600;"><i class="layui-icon layui-icon-website"></i> LayCMS</a>
            </div>
            <ul class="layui-nav layui-layout-left" style="left:210px;">
                <li class="layui-nav-item"><a href="{{ route('dashboard') }}"><i class="layui-icon layui-icon-home"></i> 首页</a></li>
                @if($cms_user->is_root || $cms_user->hasPermission('_forms', 'read'))
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-template"></i> 表单管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="{{ route('form-groups.index') }}"><i class="layui-icon layui-icon-read"></i> 表单分组</a></dd>
                        <dd><a href="{{ route('forms.index') }}"><i class="layui-icon layui-icon-file"></i> 表单列表</a></dd>
                    </dl>
                </li>
                @endif
                @if($cms_user->is_root || $cms_user->hasPermission('_users', 'read'))
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-user"></i> 用户管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="{{ route('user-groups.index') }}">用户组</a></dd>
                        <dd><a href="{{ route('users.index') }}">用户列表</a></dd>
                    </dl>
                </li>
                @endif
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="layui-icon layui-icon-username"></i> {{ $cms_user->nickname ?? $cms_user->username }}</a>
                    <dl class="layui-nav-child">
                        <dd style="padding:10px 20px;color:#999;">
                            @if($cms_user->is_root) 角色：超级管理员
                            @elseif($cms_user->userGroup) 角色：{{ $cms_user->userGroup->name }}
                            @else 角色：-
                            @endif
                        </dd>
                        <dd><a href="{{ route('logout') }}"><i class="layui-icon layui-icon-logout"></i> 退出登录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
        @php $currentTable = request()->route('tableName') ?? ''; @endphp
        <div class="layui-side layui-bg-black" style="top:60px;width:210px;">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree" lay-filter="sidebar" style="margin-top:0;">
                    @foreach($menu_form_groups ?? [] as $grp)
                    <li class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;"><i class="layui-icon layui-icon-read"></i> {{ $grp->name }}</a>
                        <dl class="layui-nav-child">
                            @foreach($grp->forms ?? [] as $f)
                            @if($cms_user->is_root || $cms_user->canAccessTable($f->table_name, 'read'))
                            <dd><a href="{{ route('table-data.index', $f->table_name) }}" class="{{ (request()->route('tableName') ?? '') === $f->table_name ? 'layui-this' : '' }}"><i class="layui-icon layui-icon-table"></i> {{ $f->name }}</a></dd>
                            @endif
                    @endforeach
                </dl>
            </li>
            @endforeach
                </ul>
            </div>
        </div>
        <div class="layui-body" style="top:60px;left:210px;bottom:0;">
            <div style="padding:20px;">
                @if(session('success'))
                <div class="layui-elem-quote layui-quote-nm layui-bg-green" style="margin-bottom:15px;">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                <div class="layui-elem-quote layui-quote-nm" style="margin-bottom:15px;background:#fff8e1;border-left:4px solid #ffc107;color:#856404;">{{ session('warning') }}</div>
                @endif
                @if($errors->any())
                <div class="layui-elem-quote layui-quote-nm layui-bg-red" style="margin-bottom:15px;">@foreach($errors->all() as $e) {{ $e }} @endforeach</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/layui.js"></script>
    <script>
    layui.config({ base: '' }).use(['element', 'layer'], function(){
        var element = layui.element;
        window.layui = layui;
        window.$ = window.jQuery = layui.$;
        element.render('nav');
    });
    </script>
    @stack('scripts')
</body>
</html>
