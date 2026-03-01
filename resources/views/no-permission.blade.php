<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>无权限 - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="background:#F5F7FA;min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div class="text-center py-5">
        <p class="display-1">403</p>
        <p>您没有权限访问此页面</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">返回首页</a>
    </div>
</body>
</html>
