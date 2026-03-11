<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/css/layui.css" rel="stylesheet">
    <link href="{{ rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') }}/css/app.css" rel="stylesheet">
    <style>
        :root { --login-primary: #003366; --login-bg: #001529; }
        body { margin: 0; min-height: 100vh; font-family: "Helvetica Neue", Helvetica, "PingFang SC", "Microsoft YaHei", sans-serif; }
        .login-wrap { display: flex; min-height: 100vh; }
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #001529 0%, #003366 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
        }
        .login-brand { font-size: 32px; font-weight: 600; margin-bottom: 12px; letter-spacing: 2px; }
        .login-slogan { font-size: 18px; color: rgba(255,255,255,.85); margin-bottom: 48px; }
        .login-features { list-style: none; padding: 0; margin: 0; }
        .login-features li { display: flex; align-items: center; font-size: 15px; color: rgba(255,255,255,.9); margin-bottom: 16px; }
        .login-features li i { margin-right: 12px; color: var(--login-primary); font-size: 18px; }
        .login-footer-left { margin-top: auto; font-size: 13px; color: rgba(255,255,255,.6); }
        .login-right {
            width: 480px; min-width: 420px;
            background: #fff;
            display: flex; flex-direction: column; justify-content: center;
            padding: 60px 56px;
            box-shadow: -4px 0 24px rgba(0,0,0,.06);
        }
        .login-title { font-size: 24px; font-weight: 600; color: #333; margin-bottom: 8px; }
        .login-subtitle { font-size: 14px; color: #666; margin-bottom: 40px; }
        .login-form .layui-input { height: 42px; }
        .login-form .layui-form-label { width: 80px; padding: 9px 15px; }
        .login-form .layui-input-block { margin-left: 100px; }
        .login-footer-right { margin-top: auto; font-size: 12px; color: #999; }
        @media (max-width: 992px) {
            .login-left { display: none; }
            .login-right { width: 100%; min-width: 0; max-width: 420px; margin: 0 auto; }
        }
        .layui-input:focus, .layui-textarea:focus{
            border-color: #001529 !important;
            box-shadow: 0 0 0 2px rgba(0,123,255,.25) !important;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-left">
            <div class="login-brand">LayCMS</div>
            <div class="login-slogan">动态表单 CMS 管理系统</div>
            <ul class="login-features">
                <li><i class="layui-icon layui-icon-template"></i> 无代码建表 · 可视化表单设计</li>
                <li><i class="layui-icon layui-icon-link"></i> 表单关联 · 外键下拉选择</li>
                <li><i class="layui-icon layui-icon-password"></i> 权限管理 · 用户组与表级权限</li>
                <li><i class="layui-icon layui-icon-edit"></i> 富文本 · 文件上传 · 多类型字段</li>
            </ul>
            <div class="login-footer-left">LayCMS © {{ date('Y') }}</div>
        </div>
        <div class="login-right">
            <h1 class="login-title">欢迎回来</h1>
            <p class="login-subtitle">登录您的账户继续使用</p>
            @if($errors->any())
            <div class="layui-elem-quote layui-quote-nm layui-bg-red" style="margin-bottom:15px;">{{ $errors->first() }}</div>
            @endif
            <form class="layui-form login-form" action="{{ route('login') }}" method="POST" style="margin-top:20px;">
                @csrf
                <div class="layui-form-item">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" required lay-verify="required" placeholder="请输入用户名" value="{{ old('username') }}" class="layui-input" autofocus>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn layui-btn-normal" style="width:100%;height:44px;">登 录</button>
                    </div>
                </div>
            </form>
            <div class="login-footer-right">LayCMS · 基于 Laravel 构建</div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/layui@2.13.4/dist/layui.js"></script>
    <script>layui.use('form', function(){ var form = layui.form; });</script>
</body>
</html>
