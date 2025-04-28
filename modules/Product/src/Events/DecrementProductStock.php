<?php

namespace Modules\Product\Events;

use Modules\Order\Checkout\OrderFulfilled;
use Modules\Product\Warehouse\ProductStockManager;

class DecrementProductStock
{
    public function __construct(
        protected ProductStockManager $productStockManager
    )
    {
    }

    public function handle(OrderFulfilled $event): void
    {
        foreach ($event->order->lines as $line) {
            $this->productStockManager->decrement($line->productId, $line->quantity);
        }
    }
}
