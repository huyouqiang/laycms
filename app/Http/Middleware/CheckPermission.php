<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->attributes->get('cms_user');
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->is_root) {
            return $next($request);
        }
        $parts = explode('.', $permission);
        $resource = $parts[0];
        $action = $parts[1] ?? 'read';
        if ($action === 'manage') {
            $action = 'read';
        }
        $tableName = in_array($resource, ['forms', 'users']) ? "_{$resource}" : $resource;
        if (!$user->hasPermission($tableName, $action)) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 403, 'msg' => '无权限'], 403);
            }
            return response()->view('no-permission', [], 403);
        }
        return $next($request);
    }
}
