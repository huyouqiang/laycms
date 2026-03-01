<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTablePermission
{
    public function handle(Request $request, Closure $next, string $action): Response
    {
        $user = $request->attributes->get('cms_user');
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->is_root) {
            return $next($request);
        }
        $tableName = $request->route('tableName');
        if (!$user->canAccessTable($tableName, $action)) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 403, 'msg' => '无权限操作此数据表'], 403);
            }
            return response()->view('no-permission', [], 403);
        }
        return $next($request);
    }
}
