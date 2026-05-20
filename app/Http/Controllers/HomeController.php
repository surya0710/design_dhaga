<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

class HomeController extends Controller
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

    public function index()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $reviews        = Testimonial::where('status', 1)->orderBy('id', 'DESC')->take(8)->get();
        $newArrivals    = Product::where('status', 1)
            ->with(['category.parent'])
            ->latest()
            ->limit(9)
            ->get();

        $bestSellers    = Product::where('status', 1)->where('featured', 2)
            ->with(['category.parent'])
            ->latest()
            ->limit(9)
            ->get();

        $pageContent    = Pages::where('slug', '/')->first();

        $highlights     = HomepageHighlight::where('status', 1)->get();

        $sliders = Sliders::where('active_status', 1)->orderBy('order', 'asc')->get();
        $homeSections = HomeSection::where('status', 1)
            ->with(['items' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        return view('frontend.home', compact('categories', 'newArrivals', 'sliders', 'bestSellers', 'menu', 'reviews', 'pageContent', 'homeSections', 'highlights'));
    }

    public function about()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $stories        = Story::where('status', 1)->orderBy('display_order', 'ASC')->get();
        $about          = AboutSection::where('status', 1)->first();
        $pageContent    = Pages::where('slug', 'about-us')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.about', compact('categories', 'menu', 'stories', 'about', 'pageContent', 'highlights'));
    }

    public function contact()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'contact-us')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.contact', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function portfolio()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'portfolio')->first();
        $portfolio      = PortfolioCategory::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $highlights     = HomepageHighlight::where('status', 1)->get();

        return view('frontend.portfolio', compact('categories', 'menu', 'portfolio', 'pageContent', 'highlights'));
    }

    public function terms()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'terms-and-condition')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.terms', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function returnPolicy()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'return-policy')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.return-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function orderShipping()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'order-shipping-policy')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.shipping-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function privacyPolicy()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'privacy-policy')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.privacy-policy', compact('categories', 'menu', 'pageContent', 'highlights'));
    }

    public function store()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.store', compact('categories', 'menu', 'highlights'));
    }

    public function collaborations()
    {
        $categories     = $this->categories;
        $menu           = $this->menu;
        $pageContent    = Pages::where('slug', 'collaborations')->first();
        $highlights     = HomepageHighlight::where('status', 1)->get();
        return view('frontend.collaborations', compact('categories', 'menu', 'pageContent', 'highlights'));
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
}
