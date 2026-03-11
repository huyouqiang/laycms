<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class FormFieldController extends Controller
{
    public function index(Form $form)
    {
        $form->load('fields');
        return view('form-fields.index', compact('form'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:cms_forms,id',
            'field_name' => 'required|string|max:64|regex:/^[a-z][a-z0-9_]*$/',
            'label' => 'required|string|max:100',
            'form_control' => 'required|in:input,input_bigint,textarea,select,radio,checkbox,date,datetime,number,file,editor,relation',
            'options' => 'nullable|string',
            'attributes' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'is_list_visible' => 'nullable|boolean',
        ], [
            'field_name.regex' => '字段名只能包含小写字母、数字和下划线',
        ]);
        $form = Form::findOrFail($validated['form_id']);
        $validated['field_name'] = strtolower($validated['field_name']);
        $validated['sort_order'] = $validated['sort_order'] ?? ($form->fields()->max('sort_order') + 1);
        $validated['is_required'] = (bool) ($validated['is_required'] ?? false);
        $validated['is_list_visible'] = (bool) ($validated['is_list_visible'] ?? true);
        if (FormField::where('form_id', $form->id)->where('field_name', $validated['field_name'])->exists()) {
            return back()->withErrors(['field_name' => '该字段已存在'])->withInput();
        }
        $field = FormField::create($validated);
        $this->addColumnToTable($form->table_name, $field);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '添加成功', 'data' => $field]);
        }
        return redirect()->route('form-fields.index', $form)->with('success', '字段添加成功');
    }

    public function update(Request $request, FormField $field)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'form_control' => 'required|in:input,input_bigint,textarea,select,radio,checkbox,date,datetime,number,file,editor,relation',
            'options' => 'nullable|string',
            'attributes' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'is_list_visible' => 'nullable|boolean',
        ]);
        $validated['is_required'] = (bool) ($validated['is_required'] ?? false);
        $validated['is_list_visible'] = (bool) ($validated['is_list_visible'] ?? true);
        $oldFormControl = $field->form_control;
        $field->update($validated);
        if ($oldFormControl !== $field->form_control) {
            $field->loadMissing('form');
            if ($field->form && Schema::hasTable($field->form->table_name) && Schema::hasColumn($field->form->table_name, $field->field_name)) {
                try {
                    $this->modifyColumnType($field->form->table_name, $field);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('修改字段类型失败', [
                        'table' => $field->form->table_name,
                        'column' => $field->field_name,
                        'type' => $this->getColumnTypeForControl($field->form_control),
                        'message' => $e->getMessage(),
                    ]);
                    $warningMsg = '数据库列类型修改失败：' . $e->getMessage();
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'code' => 0,
                            'msg' => '字段已保存',
                            'warning' => $warningMsg,
                            'redirect' => route('form-fields.index', $field->form),
                        ]);
                    }
                    return redirect()->route('form-fields.index', $field->form)
                        ->with('success', '字段已保存')
                        ->with('warning', $warningMsg);
                }
            }
        }
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('form-fields.index', $field->form)->with('success', '字段更新成功');
    }

    public function destroy(FormField $field)
    {
        $form = $field->form;
        $field->delete();
        $this->dropColumnFromTable($form->table_name, $field->field_name);
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('form-fields.index', $form)->with('success', '字段已删除');
    }

    private function getColumnTypeForControl(string $formControl): string
    {
        return match ($formControl) {
            'number', 'relation', 'input_bigint' => 'BIGINT UNSIGNED',
            'date' => 'DATE',
            'datetime' => 'DATETIME',
            'textarea', 'editor' => 'TEXT',
            'file' => 'VARCHAR(500)',
            'radio', 'checkbox', 'select' => 'VARCHAR(255)',
            default => 'VARCHAR(255)',
        };
    }

    private function addColumnToTable(string $table, FormField $field): void
    {
        $type = $this->getColumnTypeForControl($field->form_control);
        $nullable = $field->is_required ? 'NOT NULL' : 'NULL';
        DB::statement("ALTER TABLE `{$table}` ADD COLUMN `{$field->field_name}` {$type} {$nullable}");
    }

    private function modifyColumnType(string $table, FormField $field): void
    {
        $type = $this->getColumnTypeForControl($field->form_control);
        $nullable = $field->is_required ? 'NOT NULL' : 'NULL';
        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$field->field_name}` {$type} {$nullable}");
    }

    private function dropColumnFromTable(string $table, string $column): void
    {
        DB::statement("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
    }
}
