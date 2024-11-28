<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Route;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class PaymentRouteEndpointCollectionTest extends TestCase
{
    /** @test */
    public function update_release_date_for_test()
    {
        $client = new MockClient([
            UpdatePaymentRouteRequest::class => new MockResponse(200, 'route'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Route $route */
        $route = $client->paymentRoutes->updateReleaseDateFor($payment, 'rt_abc123', '2024-01-01');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->resource);
        $this->assertNotEmpty($route->id);
        $this->assertNotEmpty($route->amount);
        $this->assertNotEmpty($route->destination);
        $this->assertNotEmpty($route->releaseDate);
        $this->assertNotEmpty($route->_links);
    }
}