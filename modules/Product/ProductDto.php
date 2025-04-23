<?php

namespace Modules\Product;

use Modules\Product\Models\Product;

readonly class ProductDto
{
    public function __construct(
        public int $id,
        public int $priceInCents,
        public int $stock
    ) {}

    public static function fromEloquentModel(Product $product): ProductDto
    {
        return new self(
            id: $product->id,
            priceInCents: $product->price_in_cents,
            stock: $product->stock
        );
    }
}
