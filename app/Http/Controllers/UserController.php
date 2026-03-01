<?php

namespace App\Http\Controllers;

use App\Models\CmsUser;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = CmsUser::with('userGroup')->orderBy('id')->get();
        $groups = UserGroup::orderBy('id')->get();
        return view('users.index', compact('users', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:cms_users,username',
            'password' => 'required|string|min:6',
            'nickname' => 'nullable|string|max:50',
            'user_group_id' => 'required|exists:user_groups,id',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['is_root'] = false;
        CmsUser::create($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '用户创建成功']);
        }
        return redirect()->route('users.index')->with('success', '用户创建成功');
    }

    public function update(Request $request, CmsUser $user)
    {
        if ($user->is_root) {
            abort(403, '不能修改根用户');
        }
        $rules = [
            'nickname' => 'nullable|string|max:50',
            'user_group_id' => 'required|exists:user_groups,id',
            'is_active' => 'nullable|boolean',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }
        $validated = $request->validate($rules);
        if (isset($validated['password'])) {
            $user->password = $validated['password'];
            $user->save();
            unset($validated['password']);
        }
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $user->update($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('users.index')->with('success', '更新成功');
    }

    public function destroy(CmsUser $user)
    {
        if ($user->is_root) {
            abort(403, '不能删除根用户');
        }
        $user->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('users.index')->with('success', '用户已删除');
    }
}
