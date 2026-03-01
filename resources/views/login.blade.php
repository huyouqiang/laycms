<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') }}/css/app.css" rel="stylesheet">
    <style>
        :root { --login-primary: #1890ff; --login-bg: #001529; }
        body { margin: 0; min-height: 100vh; display: flex; font-family: "Helvetica Neue", Helvetica, "PingFang SC", "Microsoft YaHei", sans-serif; }
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
        .login-features li {
            display: flex;
            align-items: center;
            font-size: 15px;
            color: rgba(255,255,255,.9);
            margin-bottom: 16px;
        }
        .login-features li i { margin-right: 12px; color: var(--login-primary); font-size: 18px; }
        .login-footer-left { margin-top: auto; font-size: 13px; color: rgba(255,255,255,.6); }
        .login-right {
            width: 480px;
            min-width: 420px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            box-shadow: -4px 0 24px rgba(0,0,0,.06);
        }
        .login-title { font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 8px; }
        .login-subtitle { font-size: 14px; color: #6b7280; margin-bottom: 40px; }
        .login-form .form-control {
            height: 44px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        .login-form .form-control:focus {
            border-color: var(--login-primary);
            box-shadow: 0 0 0 3px rgba(24,144,255,.15);
        }
        .login-form .form-label { font-weight: 500; color: #374151; margin-bottom: 8px; }
        .login-form .btn-login {
            height: 44px;
            font-size: 16px;
            font-weight: 500;
            background: var(--login-primary);
            border: none;
            border-radius: 6px;
        }
        .login-form .btn-login:hover { background: #40a9ff; color: #fff; }
        .login-footer-right { margin-top: auto; font-size: 12px; color: #9ca3af; }
        @media (max-width: 992px) {
            .login-left { display: none; }
            .login-right { width: 100%; min-width: 0; max-width: 420px; margin: 0 auto; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="login-brand">LayCMS</div>
        <div class="login-slogan">动态表单 CMS 管理系统</div>
        <ul class="login-features">
            <li><i class="bi bi-ui-checks"></i> 无代码建表 · 可视化表单设计</li>
            <li><i class="bi bi-link-45deg"></i> 表单关联 · 外键下拉选择</li>
            <li><i class="bi bi-shield-lock"></i> 权限管理 · 用户组与表级权限</li>
            <li><i class="bi bi-file-earmark-rich-text"></i> 富文本 · 文件上传 · 多类型字段</li>
        </ul>
        <div class="login-footer-left">LayCMS © {{ date('Y') }}</div>
    </div>
    <div class="login-right">
        <h1 class="login-title">欢迎回来</h1>
        <p class="login-subtitle">登录您的账户继续使用</p>
        @if($errors->any())
        <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
        @endif
        <form class="login-form" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">用户名</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="请输入用户名" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">密码</label>
                <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login w-100">登 录</button>
        </form>
        <div class="login-footer-right">LayCMS · 基于 Laravel 构建</div>
    </div>
</body>
</html>
