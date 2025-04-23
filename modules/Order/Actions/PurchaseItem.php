<?php

namespace Modules\Order\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Modules\Order\DTOs\OrderDto;
use Modules\Order\DTOs\PendingPayment;
use Modules\Order\Events\OrderFulfilled;
use Modules\Order\Models\Order;
use Modules\Payment\Actions\CreatePaymentForOrder;
use Modules\Product\CartItemCollection;
use Modules\Product\Warehouse\ProductStockManager;
use Modules\User\UserDto;
use Throwable;

class PurchaseItem
{

    public function __construct(
        protected ProductStockManager   $productStockManager,
        protected CreatePaymentForOrder $createPaymentForOrder,
        protected DatabaseManager       $databaseManager,
        protected Dispatcher            $events,
    )
    {
    }

    /**
     * @param CartItemCollection $items
     * @param PendingPayment $pendingPayment
     * @param UserDto $user
     * @return OrderDto
     * @throws Throwable
     */
    public function handle(CartItemCollection $items, PendingPayment $pendingPayment, UserDto $user): OrderDto
    {
        $order = $this->databaseManager->transaction(function () use ($items, $user, $pendingPayment) {
            $order = Order::startForUser($user->id);
            $order->addLinesForCartItems($items);
            $order->fulfill();

            $this->createPaymentForOrder->handle(
                $order->id,
                $user->id,
                $items->totalInCents(),
                $pendingPayment->provider,
                $pendingPayment->paymentToken
            );

            return OrderDto::fromEloquentModel($order);
        });

        $this->events->dispatch(new OrderFulfilled($order, $user));

        return $order;
    }
}
