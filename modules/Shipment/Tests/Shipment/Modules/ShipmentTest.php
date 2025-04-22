<?php

namespace Modules\Order\Tests\Order\Modules;

use Modules\Shipment\Tests\ShipmentTestCase;

class ShipmentTest extends ShipmentTestCase
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
