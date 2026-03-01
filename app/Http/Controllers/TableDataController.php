<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableDataController extends Controller
{
    public function index(Request $request, string $tableName)
    {
        $form = Form::where('table_name', $tableName)->with('fields')->firstOrFail();
        $query = DB::table($tableName);
        if ($request->filled('search')) {
            $searchFields = $form->fields->where('is_list_visible', true)
                ->filter(fn ($f) => in_array($f->form_control, ['input', 'textarea', 'editor']));
            if ($searchFields->isNotEmpty()) {
                $query->where(function ($q) use ($searchFields, $request) {
                    foreach ($searchFields as $field) {
                        $q->orWhere($field->field_name, 'like', '%' . $request->search . '%');
                    }
                });
            }
        }
        $perPage = max(1, min(100, (int) $request->get('limit', 15)));
        $data = $query->orderByDesc('id')->paginate($perPage);
        if ($request->expectsJson() || $request->ajax() || $request->has('page')) {
            return response()->json([
                'code' => 0,
                'count' => $data->total(),
                'data' => $data->items(),
                'msg' => '',
            ]);
        }
        return view('table-data.index', compact('form', 'data'));
    }

    public function create(string $tableName)
    {
        $form = Form::where('table_name', $tableName)->with(['fields.relation.relatedForm.fields'])->firstOrFail();
        $row = null;
        return view('table-data.form', compact('form', 'tableName', 'row'));
    }

    public function store(Request $request, string $tableName)
    {
        $form = Form::where('table_name', $tableName)->with('fields')->firstOrFail();
        $data = $this->collectFormData($request, $form);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table($tableName)->insert($data);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '添加成功']);
        }
        return redirect()->route('table-data.index', $tableName)->with('success', '添加成功');
    }

    public function edit(string $tableName, int $id)
    {
        $form = Form::where('table_name', $tableName)->with(['fields.relation.relatedForm.fields'])->firstOrFail();
        $row = DB::table($tableName)->where('id', $id)->first();
        if (!$row) {
            abort(404);
        }
        return view('table-data.form', compact('form', 'tableName', 'row'));
    }

    public function update(Request $request, string $tableName, int $id)
    {
        $form = Form::where('table_name', $tableName)->with('fields')->firstOrFail();
        $data = $this->collectFormData($request, $form);
        $data['updated_at'] = now();
        DB::table($tableName)->where('id', $id)->update($data);
        if ($request->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '更新成功']);
        }
        return redirect()->route('table-data.index', $tableName)->with('success', '更新成功');
    }

    public function relationOptions(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'ref' => 'required|string',
            'display' => 'required|string',
            'q' => 'nullable|string|max:200',
        ]);
        $table = $request->table;
        if (!Form::where('table_name', $table)->exists()) {
            return response()->json(['data' => []]);
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
            return response()->json(['data' => []]);
        }
        $refCol = $request->ref;
        $displayCol = $request->display;
        $q = trim($request->q ?? '');
        $query = DB::table($table)->select($refCol, $displayCol)->orderBy('id');
        if ($q !== '') {
            $query->where(function ($qb) use ($displayCol, $refCol, $q) {
                $qb->where($displayCol, 'like', '%' . $q . '%')
                    ->orWhere($refCol, 'like', '%' . $q . '%');
            });
        }
        $rows = $query->limit(50)->get();
        $data = $rows->map(fn ($r) => ['value' => $r->{$refCol}, 'label' => $r->{$displayCol} ?? (string) $r->{$refCol}]);
        return response()->json(['data' => $data]);
    }

    public function destroy(string $tableName, int $id)
    {
        DB::table($tableName)->where('id', $id)->delete();
        if (request()->expectsJson()) {
            return response()->json(['code' => 0, 'msg' => '删除成功']);
        }
        return redirect()->route('table-data.index', $tableName)->with('success', '删除成功');
    }

    private function collectFormData(Request $request, Form $form): array
    {
        $data = [];
        foreach ($form->fields as $field) {
            $value = $request->input($field->field_name);
            if ($field->form_control === 'checkbox') {
                $value = is_array($value) ? json_encode($value) : $value;
            }
            if (in_array($field->form_control, ['number', 'relation'])) {
                $value = is_numeric($value) ? (int) $value : ($value === '' || $value === null ? null : $value);
            }
            if ($value !== null && $value !== '') {
                $data[$field->field_name] = $value;
            }
        }
        return $data;
    }
}
