<?php

namespace Modules\Product\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;
use Modules\Order\Checkout\OrderFulfilled;
use Modules\Product\Events\DecrementProductStock;

class EventServiceProvider extends BaseServiceProvider
{
    protected $listen = [
        OrderFulfilled::class => [
            DecrementProductStock::class,
        ]
    ];

}
