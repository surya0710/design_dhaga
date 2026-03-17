<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string',
            'review' => 'nullable|string',
        ]);

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review_title' => $request->review_title,
            'review' => $request->review,
            'approved' => false, // optional: mark as false until admin approves
        ]);

        return back()->with('success', 'Thanks for your review!');
    }

}
