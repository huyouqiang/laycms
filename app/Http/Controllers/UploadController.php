<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ], ['file.max' => '文件大小不能超过 10MB']);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $name = date('YmdHis') . '_' . \Illuminate\Support\Str::random(6) . ($ext ? '.' . $ext : '');
        $file->move(public_path('upload'), $name);
        $path = 'upload/' . $name;

        return response()->json([
            'code' => 0,
            'msg' => '上传成功',
            'path' => $path,
        ]);
    }
}
