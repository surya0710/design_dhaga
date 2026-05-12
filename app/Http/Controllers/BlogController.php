<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Menu;

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
        return view('frontend.blogs',compact('blogs', 'categories', 'menu'));
    }

    public function blogdetail($slug) {
        $categories = $this->categories;
        $blog               = Blog::where('slug', $slug)->firstOrFail();
        $featuredProducts   = Product::where('status', 1)->where('featured', 1)->with(['category.parent'])->inRandomOrder()->limit(8)->get();
        $menu               = $this->menu;
        return view('frontend.blog-details',compact('blog', 'categories', 'featuredProducts', 'menu'));
    }
}
