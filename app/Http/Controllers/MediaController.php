<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{

    public function index()
    {
        $media = Media::latest()->get();
        return response()->json($media);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048'
        ]);

        $file = $request->file('file');

        $filename = time().'_'.$file->getClientOriginalName();

        $path = $file->storeAs('uploads/media', $filename, 'public');

        $media = Media::create([
            'file_name' => $filename,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize()
        ]);

        return response()->json([
            'success' => true,
            'media' => $media
        ]);
    }
}