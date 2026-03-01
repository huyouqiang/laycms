<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class FormRelationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:cms_forms,id',
            'field_name' => 'nullable|string|max:64|regex:/^[a-z][a-z0-9_]*$/',
            'form_field_id' => 'nullable|exists:cms_form_fields,id',
            'related_form_id' => 'required|exists:cms_forms,id',
            'related_field_name' => 'required|string|max:64',
        ], ['field_name.regex' => '字段名只能包含小写字母、数字和下划线']);

        $form = Form::findOrFail($validated['form_id']);
        $relatedForm = Form::findOrFail($validated['related_form_id']);

        if ($form->id === $relatedForm->id) {
            return back()->withErrors(['related_form_id' => '不能关联到自身']);
        }

        if (Schema::hasTable($relatedForm->table_name)) {
            $columns = Schema::getColumnListing($relatedForm->table_name);
            if (!in_array($validated['related_field_name'], $columns)) {
                return back()->withErrors(['related_field_name' => '关联表中不存在该字段']);
            }
        }

        $formField = null;
        if (!empty($validated['form_field_id'])) {
            $formField = FormField::where('form_id', $form->id)->findOrFail($validated['form_field_id']);
            if (FormRelation::where('form_field_id', $formField->id)->exists()) {
                return back()->withErrors(['form_field_id' => '该字段已被用于关联']);
            }
            $formField->update(['form_control' => 'relation']);
        } else {
            $fieldName = strtolower($validated['field_name'] ?? '');
            if (!$fieldName) {
                return back()->withErrors(['field_name' => '请选择现有字段或输入新字段名']);
            }
            if (FormField::where('form_id', $form->id)->where('field_name', $fieldName)->exists()) {
                $formField = FormField::where('form_id', $form->id)->where('field_name', $fieldName)->first();
                if (FormRelation::where('form_field_id', $formField->id)->exists()) {
                    return back()->withErrors(['field_name' => '该字段已被用于关联']);
                }
                $formField->update(['form_control' => 'relation']);
            } else {
                $formField = FormField::create([
                    'form_id' => $form->id,
                    'field_name' => $fieldName,
                    'label' => $relatedForm->name . 'ID',
                    'form_control' => 'relation',
                    'sort_order' => $form->fields()->max('sort_order') + 1,
                    'is_required' => false,
                    'is_list_visible' => true,
                ]);
                $this->addColumnForRelation($form->table_name, $relatedForm->table_name, $fieldName, $validated['related_field_name']);
            }
        }

        $relation = FormRelation::create([
            'form_id' => $form->id,
            'form_field_id' => $formField->id,
            'related_form_id' => $relatedForm->id,
            'related_field_name' => $validated['related_field_name'],
        ]);

        $this->addForeignKey($form->table_name, $formField->field_name, $relatedForm->table_name, $validated['related_field_name']);

        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '关联添加成功', 'data' => $relation->load('formField', 'relatedForm')]);
        }
        return redirect()->route('forms.edit', $form)->with('success', '关联添加成功');
    }

    public function destroy(FormRelation $relation)
    {
        $form = $relation->form;
        $this->dropForeignKey($form->table_name, $relation->formField->field_name);
        $relation->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '关联已删除']);
        }
        return redirect()->route('forms.edit', $form)->with('success', '关联已删除');
    }

    public function getRelatedColumns(Form $form)
    {
        if (!Schema::hasTable($form->table_name)) {
            return response()->json(['columns' => []]);
        }
        $columns = Schema::getColumnListing($form->table_name);
        return response()->json(['columns' => $columns]);
    }

    private function getColumnType(string $table, string $column): string
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            $result = DB::selectOne("SHOW COLUMNS FROM `{$table}` WHERE Field = ?", [$column]);
            return $result ? ($result->Type ?? 'BIGINT') : 'BIGINT';
        }
        return 'BIGINT';
    }

    private function addColumnForRelation(string $table, string $relatedTable, string $column, string $relatedColumn): void
    {
        if (Schema::hasColumn($table, $column)) {
            return;
        }
        $type = $this->getColumnType($relatedTable, $relatedColumn);
        DB::statement("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$type} NULL");
    }

    private function addForeignKey(string $table, string $column, string $relatedTable, string $relatedColumn): void
    {
        $fkName = 'fk_' . $table . '_' . $column;
        if (strlen($fkName) > 64) {
            $fkName = 'fk_' . substr(md5($table . $column), 0, 16);
        }
        try {
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$column}`) REFERENCES `{$relatedTable}` (`{$relatedColumn}`) ON DELETE SET NULL ON UPDATE CASCADE");
        } catch (\Throwable $e) {
            // 可能已存在，忽略
        }
    }

    private function dropForeignKey(string $table, string $column): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table, $column]);
            foreach ($fks as $fk) {
                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }
    }
}
