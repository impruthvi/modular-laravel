<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Models\Order;
use Modules\Payment\PayBuddy;
use Modules\Product\Models\Product;

class CheckoutController
{
    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request)
    {
        $products = collect($request->input('products'))->map(function (array $productDetails) {
            return [
                'product' => Product::find($productDetails['id']),
                'quantity' => $productDetails['quantity'],
            ];
        });

        $orderTotalInCents = $products->sum(fn(array $productDetails) => $productDetails['quantity'] * $productDetails['product']->price_in_cents);

        $paybuddy = PayBuddy::make();

        try {
            $charge = $paybuddy->charge($request->input('payment_token'), $orderTotalInCents, 'Modularization');
        }catch (\RuntimeException $e) {
            throw  ValidationException::withMessages([
                'payment_token' => 'We could not complere your payment. Please try again.',
            ]);
        }

        $order = Order::query()->create([
            'payment_id' => $charge['id'],
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'total_in_cents' => $orderTotalInCents,
            'user_id' => $request->user()->id,
        ]);

        foreach ($products as $product) {
            $product['product']->decrement('stock', $product['quantity']);
            $order->lines()->create([
                'product_id' => $product['product']->id,
                'quantity' => $product['quantity'],
                'product_price_in_cents' => $product['product']->price_in_cents,
            ]);
        }

        return response()->json([], 201);
    }
}
