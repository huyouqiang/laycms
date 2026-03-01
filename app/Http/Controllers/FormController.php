<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $tableColumns = Schema::hasTable($form->table_name) ? Schema::getColumnListing($form->table_name) : [];
        $tableIndexes = $this->getTableIndexes($form->table_name);
        return view('forms.edit', compact('form', 'formGroups', 'otherForms', 'tableColumns', 'tableIndexes'));
    }

    private function getTableIndexes(string $tableName): array
    {
        if (DB::getDriverName() !== 'mysql' || !Schema::hasTable($tableName)) {
            return [];
        }
        $rows = DB::select(
            "SELECT INDEX_NAME, COLUMN_NAME, INDEX_TYPE, NON_UNIQUE FROM information_schema.STATISTICS WHERE table_schema = DATABASE() AND table_name = ? ORDER BY INDEX_NAME, SEQ_IN_INDEX",
            [$tableName]
        );
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->INDEX_NAME;
            if (!isset($grouped[$key])) {
                $type = '普通';
                if ($r->INDEX_NAME === 'PRIMARY') {
                    $type = '主键';
                } elseif ($r->INDEX_TYPE === 'FULLTEXT') {
                    $type = '全文';
                } elseif ($r->INDEX_TYPE === 'SPATIAL') {
                    $type = '空间';
                } elseif ((int) $r->NON_UNIQUE === 0) {
                    $type = '唯一';
                } else {
                    $type = '普通 (' . $r->INDEX_TYPE . ')';
                }
                $grouped[$key] = ['name' => $r->INDEX_NAME, 'columns' => [], 'type' => $type];
            }
            $grouped[$key]['columns'][] = $r->COLUMN_NAME;
        }
        return array_values(array_map(fn ($g) => ['name' => $g['name'], 'column' => implode(', ', $g['columns']), 'type' => $g['type']], $grouped));
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

    public function addIndex(Request $request, Form $form)
    {
        $request->validate(['column_name' => 'required|string|max:64']);
        $column = $request->column_name;
        if (DB::getDriverName() !== 'mysql') {
            return back()->withErrors(['column_name' => '仅支持 MySQL 数据库']);
        }
        if (!Schema::hasTable($form->table_name) || !Schema::hasColumn($form->table_name, $column)) {
            return back()->withErrors(['column_name' => '字段不存在']);
        }
        $indexName = 'idx_' . $form->table_name . '_' . $column;
        if (strlen($indexName) > 64) {
            $indexName = 'idx_' . substr(md5($form->table_name . '_' . $column), 0, 32);
        }
        $existing = DB::select(
            "SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1",
            [$form->table_name, $column]
        );
        if (!empty($existing)) {
            return back()->with('success', '该字段已有索引');
        }
        try {
            DB::statement("ALTER TABLE `{$form->table_name}` ADD INDEX `{$indexName}` (`{$column}`)");
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'already exists')) {
                return back()->with('success', '索引已存在');
            }
            return back()->withErrors(['column_name' => $e->getMessage()]);
        }
        return back()->with('success', '索引添加成功');
    }

    public function dropIndex(Request $request, Form $form)
    {
        $request->validate(['index_name' => 'required|string|max:64']);
        $indexName = $request->index_name;
        if ($indexName === 'PRIMARY') {
            return back()->withErrors(['index_name' => '不能删除主键索引']);
        }
        if (DB::getDriverName() !== 'mysql') {
            return back()->withErrors(['index_name' => '仅支持 MySQL 数据库']);
        }
        if (!Schema::hasTable($form->table_name)) {
            return back()->withErrors(['index_name' => '数据表不存在']);
        }
        $existing = DB::selectOne(
            "SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1",
            [$form->table_name, $indexName]
        );
        if (!$existing) {
            return back()->withErrors(['index_name' => '索引不存在']);
        }
        try {
            DB::statement("ALTER TABLE `{$form->table_name}` DROP INDEX `{$indexName}`");
        } catch (\Throwable $e) {
            return back()->withErrors(['index_name' => $e->getMessage()]);
        }
        return back()->with('success', '索引已删除');
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
