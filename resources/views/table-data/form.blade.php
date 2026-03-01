@extends('layouts.app')

@section('title', ($row ? '编辑' : '新增') . ' - ' . $form->name)

@section('content')
<div class="card">
    <div class="card-header">{{ $row ? '编辑' : '新增' }} {{ $form->name }}</div>
    <div class="card-body">
        <form action="{{ $row ? route('table-data.update', [$tableName, $row->id]) : route('table-data.store', $tableName) }}" method="POST">
            @csrf
            @if($row) @method('PUT') @endif
            @foreach($form->fields as $field)
            <div class="mb-3">
                <label class="form-label{{ $field->is_required ? ' required' : '' }}">{{ $field->label }}</label>
                @include('table-data.field-control', ['field' => $field, 'value' => $row?->{$field->field_name} ?? old($field->field_name) ?? ''])
            </div>
            @endforeach
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="{{ route('table-data.index', $tableName) }}" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>
@endsection
