<?php

namespace Modules\Order\Checkout;

use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail
{
    public function handle(OrderFulfilled $event)
    {
        Mail::to($event->user->email)->send(new OrderReceived($event->order->localizedTotal));
    }
}
