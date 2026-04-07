<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $companyState;
    protected string $pdfContent;

    public function __construct(Order $order, string $pdfContent, string $companyState = 'Haryana')
    {
        $this->order = $order;
        $this->pdfContent = $pdfContent;
        $this->companyState = $companyState;
    }

    public function build()
    {
        return $this->subject('Order Confirmation #' . $this->order->id)
            ->view('emails.order_completed')
            ->with([
                'order' => $this->order,
                'companyState' => $this->companyState,
            ])
            ->attachData(
                $this->pdfContent,
                'invoice-order-' . $this->order->id . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}