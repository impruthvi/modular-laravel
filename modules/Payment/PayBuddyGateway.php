<?php

namespace Modules\Payment;

use Modules\Payment\Exceptions\PaymentFailedException;

class PayBuddyGateway implements PaymentGateway
{
    public function __construct(
        protected PayBuddySdk $payBuddySdk,
    )
    {
    }

    /**
     * @param PaymentDetails $details
     * @return SuccessfulPayment
     * @throws PaymentFailedException
     */
    public function charge(PaymentDetails $details): SuccessfulPayment
    {
        try {
            $charge = $this->payBuddySdk->charge(
                $details->token,
                $details->amountInCents,
                $details->statementDescriptor
            );
        }catch (\RuntimeException){
            throw PaymentFailedException::dueToInvalidToken();
        }

        return new SuccessfulPayment(
            $charge['id'],
            $charge['amount_in_cents'],
            $this->id()
        );
    }


    public function id(): PaymentProvider
    {
        return  PaymentProvider::PayBuddy;
    }
}
