<?php

namespace App\Http\Controllers;

use App\Models\FormGroup;
use Illuminate\Http\Request;

class FormGroupController extends Controller
{
    public function index()
    {
        $groups = FormGroup::withCount('forms')->orderBy('sort_order')->orderBy('id')->get();
        return view('form-groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        FormGroup::create($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '分组创建成功']);
        }
        return redirect()->route('form-groups.index')->with('success', '分组创建成功');
    }

    public function update(Request $request, FormGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $group->update($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('form-groups.index')->with('success', '更新成功');
    }

    public function destroy(FormGroup $group)
    {
        if ($group->forms()->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['code' => 1, 'msg' => '该分组下存在表单，请先移出或删除表单'], 400);
            }
            return back()->withErrors(['error' => '该分组下存在表单，请先移出或删除表单']);
        }
        $group->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('form-groups.index')->with('success', '分组已删除');
    }
}
