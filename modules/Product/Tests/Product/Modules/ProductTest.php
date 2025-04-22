<?php

namespace Modules\Order\Tests\Product\Modules;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Product\Models\Product;
use Modules\Product\Tests\ProductTestCase;

class ProductTest extends ProductTestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $product = Product::factory()->create();

        dd($product->toArray());
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
