<?php

namespace Modules\Order\Actions;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Exceptions\PaymentFailedException;
use Modules\Order\Mail\OrderReceived;
use Modules\Order\Models\Order;
use Modules\Payment\Actions\CreatePaymentForOrder;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;
use Modules\Product\Warehouse\ProductStockManager;

class PurchaseItem
{

    public function __construct(
        protected ProductStockManager   $productStockManager,
        protected CreatePaymentForOrder $createPaymentForOrder,
        protected DatabaseManager       $databaseManager
    ) {}

    public function handle(CartItemCollection $items, PayBuddy $paymentProvider, string $paymentToken, int $userId, string $userEmail): Order
    {
        $order =  $this->databaseManager->transaction(function () use ($items, $paymentProvider, $paymentToken, $userId) {
            $order = Order::startForUser($userId);
            $order->addLinesForCartItems($items);
            $order->fulfill();

            foreach ($items->items() as $cartItem) {
                $this->productStockManager->decrement($cartItem->product->id, $cartItem->quantity);
            }

            $this->createPaymentForOrder->handle(
                $order->id,
                $userId,
                $items->totalInCents(),
                $paymentProvider,
                $paymentToken
            );

            return $order;
        });

        Mail::to($userEmail)->send(new OrderReceived($order->localizedTotal()));

        return $order;
    }
}
