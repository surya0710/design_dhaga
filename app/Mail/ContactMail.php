<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        $mail = $this->subject('New Contact Form Submission')
            ->view('emails.contact');

        if (!empty($this->contact->design)) {
            $filePath = storage_path('app/public/designs/' . $this->contact->design);

            if (file_exists($filePath)) {
                $mail->attach($filePath);
            }
        }

        return $mail;
    }
}