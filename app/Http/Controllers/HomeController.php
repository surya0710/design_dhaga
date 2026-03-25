<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ContactMail;
use App\Models\Category;

class HomeController extends Controller
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

    public function index(){
        $categories = $this->categories;
        return view('frontend.home', compact('categories'));
    }

    public function about(){
        $categories = $this->categories;
        return view('frontend.about', compact('categories'));
    }

    public function contact(){
        $categories = $this->categories;
        return view('frontend.contact', compact('categories'));
    }

    public function portfolio(){
        $categories = $this->categories;
        return view('frontend.portfolio', compact('categories'));
    }

    public function terms(){
        $categories = $this->categories;
        return view('frontend.terms', compact('categories'));
    }

    public function returnPolicy(){
        $categories = $this->categories;
        return view('frontend.return-policy', compact('categories'));
    }
    
    public function orderShipping(){
        $categories = $this->categories;
        return view('frontend.shipping-policy', compact('categories'));
    }

    public function privacyPolicy(){
        $categories = $this->categories;
        return view('frontend.privacy-policy', compact('categories'));
    }
    
    public function store(){
        $categories = $this->categories;
        return view('frontend.store', compact('categories'));
    }

    public function sendmail(Request $request)
    {
        // ✅ Validation
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:20',
            'category' => 'required|string',
            'message'  => 'required|string',
            'design'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'terms'    => 'accepted'
        ]);

        $filename = null;

        // ✅ Optional file upload
        if ($request->hasFile('design')) {
            $file = $request->file('design');
            $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/designs', $filename);
        }

        // ✅ Send mail
        Mail::to('artinfo@designdhaga.com')->send(
        // Mail::to('suryakantyadav16@gmail.com')->send(
            new ContactMail(
                $validated['name'],
                $validated['email'],
                $validated['phone'],
                $validated['message'],
                $validated['category'],
                $filename
            )
        );

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
}
