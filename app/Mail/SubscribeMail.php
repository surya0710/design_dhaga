<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscribeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subscribe;
    /**
     * Create a new message instance.
     */
    public function __construct($subscribe)
    {
        $this->subscribe = $subscribe;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Subscribe Mail',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
    public function build()
    {
        return $this->subject('Thankyou For Subscribing Ratnabhagya Newsletter')
                    ->view('emails.subscribe') // your HTML Blade view
                    ->with([
                        // 'subscribe' => $this->subscribe,
                        'token' => $this->subscribe->verification_token,
                        'email' => $this->subscribe->email,
                        'unsubscribe_token' => $this->subscribe->unsubscribe_token,
                    ]);
    }
}
