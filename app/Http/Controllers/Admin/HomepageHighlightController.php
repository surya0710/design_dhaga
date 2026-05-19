<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageHighlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageHighlightController extends Controller{
    public function index()
    {
        $highlights = HomepageHighlight::orderBy('sort_order', 'ASC')->paginate(10);

        return view('admin.homepage-highlights.index', compact('highlights'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'emoji' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $emojiPath = null;

        if ($request->hasFile('emoji')) {
            $emojiPath = $request->file('emoji')->store('homepage-highlights', 'public');
        }

        HomepageHighlight::create([
            'title' => $request->title,
            'emoji' => $emojiPath,
            'alt_text' => $request->alt_text,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->back()->with('success', 'Highlight added successfully.');
    }

    public function update(Request $request, $id)
    {
        $highlight = HomepageHighlight::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'emoji' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('emoji')) {

            if ($highlight->emoji && Storage::disk('public')->exists($highlight->emoji)) {
                Storage::disk('public')->delete($highlight->emoji);
            }

            $highlight->emoji = $request->file('emoji')->store('homepage-highlights', 'public');
        }

        $highlight->title = $request->title;
        $highlight->alt_text = $request->alt_text;
        $highlight->sort_order = $request->sort_order ?? 0;
        $highlight->status = $request->status ?? 1;

        $highlight->save();

        return redirect()->back()->with('success', 'Highlight updated successfully.');
    }

    public function destroy($id)
    {
        $highlight = HomepageHighlight::findOrFail($id);

        if ($highlight->emoji && Storage::disk('public')->exists($highlight->emoji)) {
            Storage::disk('public')->delete($highlight->emoji);
        }

        $highlight->delete();

        return redirect()->back()->with('success', 'Highlight deleted successfully.');
    }
}