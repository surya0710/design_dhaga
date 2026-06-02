<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display all pages
     */
    public function index(Request $request)
    {
        $query = Pages::query();

        $search = trim((string) $request->get('search', ''));

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('meta_title', 'like', '%' . $search . '%')
                    ->orWhere('meta_description', 'like', '%' . $search . '%')
                    ->orWhere('meta_keywords', 'like', '%' . $search . '%');
            });
        }

        $pages = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pages', compact('pages'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.pages-create');
    }

    /**
     * Store new page
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|max:255',
            'content'           => 'nullable',
            'meta_title'        => 'nullable|max:255',
            'meta_description'  => 'nullable',
            'meta_keywords'     => 'nullable',
            'canonical_url'     => 'nullable|max:255',
            'meta_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'status'            => 'required',
            'url'               => 'required',
            'heading'           => 'nullable'
        ]);

        $page = new Pages();

        $page->title = $request->title;
        $page->slug = $request->url;
        $page->heading = $request->heading;
        $page->content = $request->content;

        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->canonical_url = $request->canonical_url;

        $page->status = $request->status;

        // Upload Meta Image
        if ($request->hasFile('meta_image')) {

            $image = $request->file('meta_image');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/pages'), $imageName);

            $page->meta_image = 'uploads/pages/' . $imageName;
        }

        $page->save();

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $page = Pages::findOrFail($id);

        return view('admin.pages-edit', compact('page'));
    }

    /**
     * Update page
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'             => 'required|max:255',
            'content'           => 'nullable',
            'meta_title'        => 'nullable|max:255',
            'meta_description'  => 'nullable',
            'meta_keywords'     => 'nullable',
            'canonical_url'     => 'nullable|max:255',
            'meta_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'status'            => 'required',
            'url'               => 'required',
            'heading'           => 'nullable'
        ]);

        $page = Pages::findOrFail($id);

        $page->title = $request->title;
        $page->slug = $request->url;
        $page->heading = $request->heading;
        $page->content = $request->content;

        $page->meta_title = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords = $request->meta_keywords;
        $page->canonical_url = $request->canonical_url;

        $page->status = $request->status;

        // Upload Meta Image
        if ($request->hasFile('meta_image')) {

            // Delete old image
            if ($page->meta_image && file_exists(public_path($page->meta_image))) {
                unlink(public_path($page->meta_image));
            }

            $image = $request->file('meta_image');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/pages'), $imageName);

            $page->meta_image = 'uploads/pages/' . $imageName;
        }

        $page->save();

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Delete page
     */
    public function destroy($id)
    {
        $page = Pages::findOrFail($id);

        // Delete image
        if ($page->meta_image && file_exists(public_path($page->meta_image))) {
            unlink(public_path($page->meta_image));
        }

        $page->delete();

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Page deleted successfully.');
    }
}
