<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Models\Order;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;
use Modules\Product\Warehouse\ProductStockManager;

class CheckoutController
{
    public function __construct(
        protected ProductStockManager $productStockManager,
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request)
    {

        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));
        $orderTotalInCents = $cartItems->totalInCents();
        $paybuddy = PayBuddy::make();

        try {
            $charge = $paybuddy->charge($request->input('payment_token'), $orderTotalInCents, 'Modularization');
        } catch (\RuntimeException $e) {
            throw  ValidationException::withMessages([
                'payment_token' => 'We could not complere your payment. Please try again.',
            ]);
        }

        $order = Order::query()->create([
            'payment_id' => $charge['id'],
            'status' => 'completed',
            'total_in_cents' => $orderTotalInCents,
            'user_id' => $request->user()->id,
        ]);

        foreach ($cartItems->items() as $cartItem) {

            $this->productStockManager->decrement($cartItem->product->id, $cartItem->quantity);

            $order->lines()->create([
                'product_id' => $cartItem->product->id,
                'quantity' => $cartItem->quantity,
                'product_price_in_cents' => $cartItem->product->priceInCents,
            ]);
        }

        $payment = $order->payments()->create([
            'total_in_cents' => $orderTotalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([], 201);
    }
}
