<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Category;

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
        $categories = $this->categories;
        $blogs = Blog::orderBy('id','desc')->paginate(15);
        return view('frontend.blogs',compact('blogs', 'categories'));
    }
    public function tagIndex($slug){
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $blogs = $tag->blogs()->with('tags')->where('status', 1)->latest()->paginate(15);
        return view('blogs', compact('blogs', 'tag'));
    }  
    public function blogdetail($slug) {
        $categories = $this->categories;
        $blogs = Blog::orderBy('id','desc')->paginate(3);
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('frontend.blog-details',compact('blog', 'blogs', 'categories'));
    }
}
