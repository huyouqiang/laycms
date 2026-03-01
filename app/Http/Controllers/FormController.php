<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::with('formGroup')->withCount('fields')->orderBy('sort_order')->get();
        return view('forms.index', compact('forms'));
    }

    public function create()
    {
        $formGroups = FormGroup::orderBy('sort_order')->get();
        return view('forms.create', compact('formGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'table_name' => 'required|string|max:100|regex:/^[a-z][a-z0-9_]*$/|unique:cms_forms,table_name',
            'description' => 'nullable|string|max:255',
            'form_group_id' => 'required|exists:cms_form_groups,id',
        ]);
        $validated['table_name'] = strtolower($validated['table_name']);
        $form = Form::create($validated);
        $driver = DB::getDriverName();
        $sql = $driver === 'mysql'
            ? "CREATE TABLE `{$form->table_name}` (`id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, `created_at` timestamp NULL, `updated_at` timestamp NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            : "CREATE TABLE {$form->table_name} (id INTEGER PRIMARY KEY AUTOINCREMENT, created_at datetime, updated_at datetime)";
        DB::statement($sql);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '创建成功', 'data' => $form]);
        }
        return redirect()->route('forms.index')->with('success', '表单创建成功');
    }

    public function edit(Form $form)
    {
        $form->load(['fields', 'relations.formField', 'relations.relatedForm']);
        $formGroups = FormGroup::orderBy('sort_order')->get();
        $otherForms = Form::where('id', '!=', $form->id)->orderBy('sort_order')->get();
        return view('forms.edit', compact('form', 'formGroups', 'otherForms'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'table_name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_]*$/', Rule::unique('cms_forms', 'table_name')->ignore($form->id)],
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'form_group_id' => 'required|exists:cms_form_groups,id',
        ]);
        $validated['table_name'] = strtolower($validated['table_name']);
        $form->update($validated);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('forms.index')->with('success', '表单更新成功');
    }

    public function destroy(Form $form)
    {
        DB::statement("DROP TABLE IF EXISTS `{$form->table_name}`");
        $form->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('forms.index')->with('success', '表单已删除');
    }
}
