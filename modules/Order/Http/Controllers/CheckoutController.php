<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Modules\Order\Actions\PurchaseItem;
use Modules\Order\Exceptions\PaymentFailedException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;

class CheckoutController
{
    public function __construct(
        protected PurchaseItem $purchaseItem,
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request)
    {
        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));;

        try {
            $order = $this->purchaseItem->handle(
                $cartItems,
                PayBuddy::make(),
                $request->input('payment_token'),
                $request->user()->id,
                $request->user()->email
            );
        }catch (PaymentFailedException $e) {
            throw ValidationException::withMessages([
                'payment_token' => [
                    'The given payment token is invalid.',
                ],
            ]);
        }

        return response()->json([
            'order_url' => $order->url(),
        ], 201);
    }
}
