<?php

namespace Modules\Payment\Actions;

use Modules\Order\Exceptions\PaymentFailedException;
use Modules\Payment\PayBuddySdk;
use Modules\Payment\Payment;

class CreatePaymentForOrder
{
    /**
     * @throws PaymentFailedException
     */
    public function handle(
        int         $orderId,
        int         $userId,
        int         $totalInCents,
        PayBuddySdk $paymentProvider,
        string      $paymentToken
    ): Payment {
        try {
            $charge = $paymentProvider->charge($paymentToken, $totalInCents, 'Modularization');
        } catch (\RuntimeException $e) {
            throw PaymentFailedException::dueToInvalidToken();
        }

        return Payment::query()->create([
            'total_in_cents' => $totalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
            'user_id' => $userId,
            'order_id' => $orderId,
        ]);
    }
}
