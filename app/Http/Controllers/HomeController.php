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
        $categories = $this->categories;
        $menu       = $this->menu;
        $reviews    = Testimonial::where('status', 1)->orderBy('id', 'DESC')->take(8)->get();
        $newArrivals = Product::where('status', 1)
            ->with(['category.parent'])
            ->latest()
            ->limit(9)
            ->get();

        $bestSellers = Product::where('status', 1)->where('featured', 2)
            ->with(['category.parent'])
            ->latest()
            ->limit(9)
            ->get();

        $sliders = Sliders::where('active_status', 1)->orderBy('order', 'asc')->get();

        return view('frontend.home', compact('categories', 'newArrivals', 'sliders', 'bestSellers', 'menu', 'reviews'));
    }

    public function about()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.about', compact('categories', 'menu'));
    }

    public function contact()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.contact', compact('categories', 'menu'));
    }

    public function portfolio()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.portfolio', compact('categories', 'menu'));
    }

    public function terms()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.terms', compact('categories', 'menu'));
    }

    public function returnPolicy()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.return-policy', compact('categories', 'menu'));
    }

    public function orderShipping()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.shipping-policy', compact('categories', 'menu'));
    }

    public function privacyPolicy()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.privacy-policy', compact('categories', 'menu'));
    }

    public function store()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.store', compact('categories', 'menu'));
    }

    public function collaborations()
    {
        $categories = $this->categories;
        $menu       = $this->menu;
        return view('frontend.collaborations', compact('categories', 'menu'));
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