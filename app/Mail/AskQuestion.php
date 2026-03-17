<?php

namespace App\Mail;

use App\Models\AskQuestion as ModelsAskQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AskQuestion extends Mailable
{
    use Queueable, SerializesModels;
    public $question;
    /**
     * Create a new message instance.
     */
    public function __construct(ModelsAskQuestion $question)
    {
        $this->question = $question;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Ask Question',
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
        return $this->subject('Ask a Question Confirmation - Ratnabhagya')
                    ->view('emails.askquestion');
    }
}
