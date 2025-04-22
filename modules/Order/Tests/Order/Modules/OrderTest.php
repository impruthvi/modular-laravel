<?php

namespace Modules\Order\Tests\Order\Modules;

use Modules\Order\Tests\OrderTestCase;

class OrderTest extends OrderTestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
