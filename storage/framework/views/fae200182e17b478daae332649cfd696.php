<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - LayCMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <style>
        body { background: #F5F7FA; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="card" style="width: 400px; box-shadow: 0 2px 12px rgba(0,0,0,.1);">
        <div class="card-body p-4">
            <h5 class="card-title text-center mb-4">LayCMS 后台登录</h5>
            <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">用户名</label>
                    <input type="text" name="username" class="form-control" value="<?php echo e(old('username')); ?>" placeholder="请输入用户名" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">密码</label>
                    <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">登录</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH /opt/homebrew/var/www/laycms/resources/views/login.blade.php ENDPATH**/ ?>