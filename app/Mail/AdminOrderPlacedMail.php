<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $companyState;

    public function __construct(Order $order, string $companyState = 'Haryana')
    {
        $this->order = $order;
        $this->companyState = $companyState;
    }

    public function build()
    {
        return $this->subject('New Order Received #' . $this->order->id)
            ->view('emails.admin_order_placed')
            ->with([
                'order' => $this->order,
                'companyState' => $this->companyState,
            ]);
    }
}
