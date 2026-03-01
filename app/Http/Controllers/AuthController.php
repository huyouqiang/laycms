<?php

namespace App\Http\Controllers;

use App\Models\CmsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $user = CmsUser::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['code' => 400, 'msg' => '用户名或密码错误']);
            }
            return back()->withErrors(['username' => '用户名或密码错误'])->withInput();
        }
        if (!$user->is_active) {
            return back()->withErrors(['username' => '账号已被禁用'])->withInput();
        }
        Session::put('cms_user_id', $user->id);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '登录成功', 'redirect' => route('dashboard')]);
        }
        return redirect()->intended(route('dashboard'));
    }

    public function logout()
    {
        Session::forget('cms_user_id');
        return redirect()->route('login');
    }
}
