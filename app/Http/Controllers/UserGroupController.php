<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        $groups = UserGroup::withCount('users')->orderBy('id')->get();
        return view('user-groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);
        UserGroup::create($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '用户组创建成功']);
        }
        return redirect()->route('user-groups.index')->with('success', '用户组创建成功');
    }

    public function update(Request $request, UserGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);
        $group->update($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('user-groups.index')->with('success', '更新成功');
    }

    public function destroy(UserGroup $group)
    {
        if ($group->users()->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['code' => 1, 'msg' => '该用户组下存在用户，无法删除'], 400);
            }
            return back()->withErrors(['error' => '该用户组下存在用户，无法删除']);
        }
        $group->permissions()->delete();
        $group->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('user-groups.index')->with('success', '用户组已删除');
    }
}
