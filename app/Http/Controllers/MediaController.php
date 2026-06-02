<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{

    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 24);
        $perPage = max(1, min($perPage, 60));
        $search = trim((string) $request->get('search', ''));
        $sort = $request->get('sort') === 'oldest' ? 'asc' : 'desc';

        $media = Media::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('file_name', 'like', '%' . $search . '%')
                        ->orWhere('file_path', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', $sort)
            ->orderBy('id', $sort)
            ->paginate($perPage);

        return response()->json([
            'data' => $media->items(),
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'per_page' => $media->perPage(),
            'total' => $media->total(),
            'has_more' => $media->hasMorePages(),
        ]);
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
