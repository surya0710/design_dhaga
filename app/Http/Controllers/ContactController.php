<?php

namespace App\Http\Controllers;

use App\Mail\AskQuestion as MailAskQuestion;
use App\Mail\ContactMail;
use App\Models\AskQuestion;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index() {
        return view('contact');
    }

    public function indexsubmit(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($validated);

        // Send email
        Mail::to($contact->email)->bcc('info@ratnabhagya.com')->send(new ContactMail($contact));
        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

    public function questionsubmit(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|string|max:20',
            'message' => 'required|string',
            'product_id' => 'nullable',
            'product_name' => 'nullable|string',
            'user_id' => 'nullable'
        ]);

        $question = AskQuestion::create($validated);

        // Send email
        Mail::to($question->email)->bcc('info@ratnabhagya.com')->send(new MailAskQuestion($question));
        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
}
