<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Mail\ContactMail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contact;
use App\Models\Sliders;
use App\Models\Menu;
use App\Models\Testimonial;
use App\Models\Story;
use App\Models\AboutSection;
use App\Models\HomeSection;
use App\Models\PortfolioCategory;
use App\Models\Pages;
use App\Models\HomepageHighlight;
use App\Models\Wishlist;
use App\Services\InstagramFeedService;

class HomeController extends Controller
{
    protected $categories;
    protected $menu;

    public function __construct()
    {
        $this->categories = Cache::remember('site.active_parent_categories', 60, function () {
            return Category::where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('parent_id')
                        ->orWhere('parent_id', 0);
                })
                ->with('children')
                ->get();
        });

        $this->menu = Cache::remember('site.active_menus', 60, function () {
            return Menu::where('is_active', 1)->orderBy('created_at', 'asc')->get();
        });
    }

    public function index()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $reviews        = Cache::remember('home.reviews', 60, function () {
            return Testimonial::where('status', 1)->orderBy('id', 'DESC')->take(8)->get();
        });
        $newArrivals    = $this->mixedNewArrivals();

        $bestSellers    = Product::where('status', 1)->where('featured', 2)
            ->select('id', 'name', 'slug', 'regular_price', 'sale_price', 'image', 'category_id')
            ->with(['category.parent', 'activeVariants:id,product_id,price'])
            ->latest()
            ->limit(9)
            ->get();

        $pageContent    = Cache::remember('home.page_content', 60, function () {
            return Pages::where('slug', '/')->first();
        });

        $highlights     = $this->activeHighlights();

        $sliders = Cache::remember('home.sliders', 60, function () {
            return Sliders::where('active_status', 1)->orderBy('order', 'asc')->get();
        });

        $homeSections = Cache::remember('home.sections', 60, function () {
            return HomeSection::where('status', 1)
                ->with(['items' => function ($query) {
                    $query->where('status', 1);
                }])
                ->orderBy('sort_order')
                ->get()
                ->keyBy('key');
        });

        $instagramFeed = $homeSections->get('instagram_feed');

        if (! $instagramFeed) {
            $instagramFeed = HomeSection::where('key', 'instagram_feed')
                ->where('status', 1)
                ->with(['items' => function ($query) {
                    $query->where('status', 1);
                }])
                ->first();
        }
        $instagramProfile = null;
        $instagramPosts = collect();

        if ($instagramFeed) {
            $instagramService = app(InstagramFeedService::class);
            $instagramProfile = $instagramService->getProfile($instagramFeed);
            $instagramPosts = $instagramService->getPosts($instagramFeed);
        }

        $wishlistProductIds = [];

        if (auth()->check()) {
            $homeProductIds = $newArrivals->pluck('id')
                ->merge($bestSellers->pluck('id'))
                ->unique()
                ->values();

            $wishlistProductIds = Wishlist::where('user_id', auth()->id())
                ->whereIn('product_id', $homeProductIds)
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return view('frontend.home', compact('categories', 'newArrivals', 'sliders', 'bestSellers', 'menu', 'reviews', 'pageContent', 'homeSections', 'highlights', 'wishlistProductIds', 'instagramFeed', 'instagramProfile', 'instagramPosts'));
    }

    private function mixedNewArrivals(int $limit = 9)
    {
        $products = Product::where('status', 1)
            ->select('id', 'name', 'slug', 'regular_price', 'sale_price', 'image', 'category_id', 'created_at')
            ->with(['category.parent', 'activeVariants:id,product_id,price'])
            ->latest()
            ->limit(max($limit * 8, 40))
            ->get();

        $groups = $products
            ->groupBy(fn ($product) => (string) ($product->category_id ?? 'uncategorized'))
            ->shuffle();

        $mixed = collect();

        while ($mixed->count() < $limit && $groups->isNotEmpty()) {
            $groups = $groups->map(function ($group) use ($mixed, $limit) {
                if ($mixed->count() >= $limit) {
                    return $group;
                }

                $mixed->push($group->shift());

                return $group;
            })->filter(fn ($group) => $group->isNotEmpty());
        }

        return $mixed->shuffle()->take($limit)->values();
    }

    public function about()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $stories        = Story::where('status', 1)->orderBy('display_order', 'ASC')->get();
        $about          = AboutSection::where('status', 1)->first();
        $pageContent    = Pages::where('slug', 'about-us')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.about', compact('categories', 'menu', 'stories', 'about', 'pageContent', 'highlights'));
    }

    public function contact()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'contact-us')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.contact', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function portfolio($slug = null)
    {
        $categories     = $this->categories;
        $menu           = $this->menu;

        $pageContent = Pages::where('slug', 'portfolio')->first();

        $highlights = $this->activeHighlights();

        // ONLY categories for top navigation
        $portfolio = PortfolioCategory::select('id', 'name', 'slug', 'image')->where('status', 1)->get();

        // Active category only
        $activeCategory = PortfolioCategory::with([ 
            'subcategories' => function ($query) {
                $query->select('id', 'portfolio_category_id', 'name', 'slug');
            },

            'subcategories.galleries' => function ($query) {
                $query->select('id', 'portfolio_subcategory_id', 'image', 'title', 'alt_text');
            }

        ])
        ->where('slug', $slug ?? $portfolio->first()?->slug)
        ->firstOrFail();

        return view('frontend.portfolio', compact(
            'categories',
            'menu',
            'pageContent',
            'highlights',
            'portfolio',
            'activeCategory'
        ));
    }

    public function terms()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'terms-and-condition')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.terms', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function returnPolicy()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'return-policy')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.return-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function orderShipping()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'order-shipping-policy')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.shipping-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function privacyPolicy()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'privacy-policy')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.privacy-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function store()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        $highlights     = $this->activeHighlights();
        return view('frontend.store', compact('categories', 'menu', 'highlights'));
    }

    public function collaborations()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'collaborations')->first();
        $highlights     = $this->activeHighlights();
        return view('frontend.collaborations', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function notfound(){
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'collaborations')->first();
        $highlights     = $this->activeHighlights();
        return view('404', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function sendmail(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
            'category'   => 'required|string|max:255',
            'message'    => 'required|string',
            'design'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'terms'      => 'accepted',
        ]);

        $filename = null;

        if ($request->hasFile('design')) {
            $file = $request->file('design');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/designs', $filename);
        }

        $contact = Contact::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'mobile'     => $validated['phone'],
            'category'   => $validated['category'],
            'message'    => $validated['message'],
            'design'     => $filename,
        ]);

        Mail::to('suryakantyadav16@gmail.com')->send(new ContactMail($contact));

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

    private function activeHighlights()
    {
        return Cache::remember('site.homepage_highlights', 60, function () {
            return HomepageHighlight::where('status', 1)->get();
        });
    }
}
