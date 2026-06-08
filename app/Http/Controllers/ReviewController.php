<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'image' => 'nullable|image|max:2048|mimetypes:image/jpeg,image/png,image/webp,image/jpg',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'image' => $imagePath,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You can only edit your own review.'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'image' => 'nullable|image|max:2048|mimetypes:image/jpeg,image/png,image/webp,image/jpg',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->has('remove_image') && $review->image) {
            Storage::disk('public')->delete($review->image);
            $review->image = null;
        }

        if ($request->hasFile('image')) {
            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }
            $review->image = $request->file('image')->store('reviews', 'public');
        }

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'image' => $review->image,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You can only delete your own review.'], 403);
        }

        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return response()->json(['success' => true]);
    }

    public function adminIndex()
    {
        $reviews = Review::with(['user:id,name,email', 'product:id,name'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function adminDestroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return redirect()->route('admin.reviews')->with('status', 'Review deleted successfully');
    }
}
