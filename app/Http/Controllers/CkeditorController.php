<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ckeditor'), $filename);

            $url = asset('uploads/ckeditor/' . $filename);

            $funcNum = $request->input('CKEditorFuncNum');

            $message = 'Image uploaded successfully';

            return response(
                "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>"
            )->header('Content-Type', 'text/html; charset=utf-8');
        }

        $funcNum = $request->input('CKEditorFuncNum');
        $message = 'Image upload failed';

        return response(
            "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '', '$message');</script>"
        )->header('Content-Type', 'text/html; charset=utf-8');
    }
}