<?php

namespace Modules\Order\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Order\OrderLine;

class OrderLineDto
{
    public function __construct(
        public int $productId,
        public int $productPriceInCents,
        public int $quantity,
    )
    {
    }

    public static function fromEloquentModel(OrderLine $line): self
    {
        return new self(
            productId: $line->product_id,
            productPriceInCents: $line->product_price_in_cents,
            quantity: $line->quantity,
        );
    }

    public static function fromEloquentCollection(Collection $orderLines): array
    {
        return $orderLines->map(fn(OrderLine $line) => self::fromEloquentModel($line))->toArray();
    }

}
