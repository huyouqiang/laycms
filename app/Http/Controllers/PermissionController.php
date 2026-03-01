<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\GroupPermission;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function edit(UserGroup $group)
    {
        $group->load('permissions');
        $forms = Form::orderBy('sort_order')->get();
        $tables = ['_forms' => '表单管理', '_users' => '用户管理'];
        foreach ($forms as $f) {
            $tables[$f->table_name] = $f->name;
        }
        $perms = $group->permissions->keyBy('table_name');
        return view('permissions.edit', compact('group', 'tables', 'perms'));
    }

    public function update(Request $request, UserGroup $group)
    {
        $forms = Form::orderBy('sort_order')->get();
        $allTables = ['_forms' => 1, '_users' => 1];
        foreach ($forms as $f) {
            $allTables[$f->table_name] = 1;
        }
        $submitted = $request->input('permissions', []);
        foreach (array_keys($allTables) as $tableName) {
            $actions = $submitted[$tableName] ?? [];
            $perm = $group->permissions()->firstOrNew(['table_name' => $tableName]);
            $perm->can_create = !empty($actions['create']);
            $perm->can_read = !empty($actions['read']);
            $perm->can_update = !empty($actions['update']);
            $perm->can_delete = !empty($actions['delete']);
            $perm->save();
        }
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '权限保存成功']);
        }
        return redirect()->route('user-groups.index')->with('success', '权限保存成功');
    }
}
