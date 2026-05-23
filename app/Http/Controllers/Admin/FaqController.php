<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faqs as Faq;

class FaqController extends Controller
{
    /**
     * FAQ Listing
     */
    public function index(Request $request)
    {
        $query = Faq::query();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhere('page_slug', 'like', "%{$search}%");

            });
        }

        $list = $query->latest()->paginate(10);

        return view('admin.faq.index', compact('list'));
    }

    /**
     * Create Page
     */
    public function create()
    {
        return view('admin.faq.create');
    }

    /**
     * Store FAQ
     */
    public function store(Request $request)
    {
        $request->validate([
            'page_slug' => 'required|string|max:255',
            'question'  => 'required|string|max:255',
            'answer'    => 'required',
            'sort_order'=> 'nullable|integer',
            'status'    => 'required|boolean',
        ]);

        Faq::create([
            'page_slug' => $request->page_slug,
            'question'  => $request->question,
            'answer'    => $request->answer,
            'sort_order'=> $request->sort_order ?? 0,
            'status'    => $request->status,
        ]);

        return redirect()
                ->route('admin.faqs')
                ->with('success', 'FAQ created successfully.');
    }

    /**
     * Edit FAQ
     */
    public function edit($id)
    {
        $faq = Faq::findOrFail($id);

        return view('admin.faq.edit', compact('faq'));
    }

    /**
     * Update FAQ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'page_slug' => 'required|string|max:255',
            'question'  => 'required|string|max:255',
            'answer'    => 'required',
            'sort_order'=> 'nullable|integer',
            'status'    => 'required|boolean',
        ]);

        $faq = Faq::findOrFail($id);

        $faq->update([
            'page_slug' => $request->page_slug,
            'question'  => $request->question,
            'answer'    => $request->answer,
            'sort_order'=> $request->sort_order ?? 0,
            'status'    => $request->status,
        ]);

        return redirect()
                ->route('admin.faqs')
                ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Delete FAQ
     */
    public function delete($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return redirect()
                ->route('admin.faqs')
                ->with('success', 'FAQ deleted successfully.');
    }
}