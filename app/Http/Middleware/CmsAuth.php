<?php

namespace App\Http\Middleware;

use App\Models\Form;
use App\Models\FormGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\CmsUser;
use Symfony\Component\HttpFoundation\Response;

class CmsAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Session::get('cms_user_id');
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 401, 'msg' => '请先登录'], 401);
            }
            return redirect()->route('login');
        }
        $user = CmsUser::find($userId);
        if (!$user || !$user->is_active) {
            Session::forget('cms_user_id');
            return redirect()->route('login');
        }
        $request->attributes->set('cms_user', $user);
        View::share('cms_user', $user);
        View::share('menu_form_groups', FormGroup::with(['forms' => fn($q) => $q->orderBy('sort_order')])->orderBy('sort_order')->get());
        return $next($request);
    }
}
