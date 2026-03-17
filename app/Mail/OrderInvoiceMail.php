<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

     public $order;
    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Order Invoice Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         markdown: 'invoice',
    //         with: [
    //             'order' => $this->order,
    //         ],
    //     );
    // }
    public function build()
    {
        return $this->subject('Order Invoice Mail - Ratnabhagya')
                    ->view('emails.invoice') // your HTML Blade view
                    ->with([
                        'order' => $this->order,
                    ]);
    }
    // public function build()
    // {
    //     // Generate PDF from blade view
    //     $pdf = Pdf::loadView('emails.invoice', ['order' => $this->order]);
    //             // ->setPaper('a4')
    //             // ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    //     return $this->subject('Your Order Invoice - Ratnabhagya')
    //         ->view('emails.invoice-email')
    //         ->attachData($pdf->output(), 'invoice-RB' . $this->order->id . '.pdf', [
    //             'mime' => 'application/pdf',
    //         ]);
    // }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
