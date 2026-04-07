<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class BlogController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->with('children')
            ->get();
    }
    
    public function index() {
        $categories         = $this->categories;
        $blogs              = Blog::orderBy('id','desc')->paginate(15);
        return view('frontend.blogs',compact('blogs', 'categories'));
    }

    public function blogdetail($slug) {
        $categories = $this->categories;
        $blog               = Blog::where('slug', $slug)->firstOrFail();
        $featuredProducts   = Product::where('status', 1)->where('featured', 1)->with(['category.parent'])->inRandomOrder()->limit(8)->get();
        return view('frontend.blog-details',compact('blog', 'categories', 'featuredProducts'));
    }
}
