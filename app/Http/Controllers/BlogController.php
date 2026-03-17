<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index() {
        $blogs = Blog::orderBy('id','desc')->paginate(15);
        return view('frontend.blogs',compact('blogs'));
    }
    public function tagIndex($slug){
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $blogs = $tag->blogs()->with('tags')->where('status', 1)->latest()->paginate(15);
        return view('blogs', compact('blogs', 'tag'));
    }  
    public function blogdetail($slug) {
        $blogs = Blog::orderBy('id','desc')->paginate(3);
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('frontend.blog-details',compact('blog', 'blogs'));
    }
}
