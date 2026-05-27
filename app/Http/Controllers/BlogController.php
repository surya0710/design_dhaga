<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Menu;
use App\Models\HomepageHighlight;

class BlogController extends Controller
{
    protected $categories;
    protected $menu;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
            })
            ->with('children')
            ->get();
        
        $this->menu = Menu::where('is_active', 1)->orderBy('created_at', 'asc')->get();
    }
    
    public function index() {
        $categories         = $this->categories;
        $blogs              = Blog::orderBy('id','desc')->paginate(15);
        $menu               = $this->menu;
        $highlights         = HomepageHighlight::where('status', 1)->get();
        return view('frontend.blogs',compact('blogs', 'categories', 'menu', 'highlights'));
    }

    public function blogdetail($slug) {
        $categories = $this->categories;
        $blog               = Blog::where('slug', $slug)->firstOrFail();
        $recentBlogs        = Blog::orderBy('id','desc')->limit(6)->get();
        $featuredProducts   = Product::where('status', 1)->where('featured', 1)->with(['category.parent'])->inRandomOrder()->limit(8)->get();
        $menu               = $this->menu;
        $highlights         = HomepageHighlight::where('status', 1)->get();
        return view('frontend.blog-details',compact('blog', 'recentBlogs', 'categories', 'featuredProducts', 'menu', 'highlights'));
    }
}
