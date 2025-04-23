<?php

namespace Modules\Order\Events;

use Modules\Order\DTOs\OrderDto;
use Modules\User\UserDto;

readonly class OrderFulfilled
{
    /**
     * @param OrderDto $order
     * @param UserDto $user
     */
    public function __construct(
        public OrderDto $order,
        public UserDto  $user,
    )
    {
    }
}
