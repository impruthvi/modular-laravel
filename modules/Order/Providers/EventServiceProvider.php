<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;
use Modules\Order\Events\OrderFulfilled;
use Modules\Order\Events\SendOrderConfirmationEmail;

class EventServiceProvider extends BaseServiceProvider
{
    protected $listen = [
      OrderFulfilled::class => [
          SendOrderConfirmationEmail::class,
      ]
    ];
}
