<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Date;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use Mollie\Api\Resources\Route;
use PHPUnit\Framework\TestCase;

class UpdatePaymentRouteRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment_route()
    {
        $client = new MockMollieClient([
            UpdatePaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        $request = new UpdatePaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP', new Date('2024-01-01'));

        /** @var Route */
        $route = $client->send($request);

        $this->assertTrue($route->getResponse()->successful());
        $this->assertInstanceOf(Route::class, $route);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP', new Date('2024-01-01'));

        $this->assertEquals('payments/tr_WDqYK6vllg/routes/rt_H2wvxEyQcP', $request->resolveResourcePath());
    }
}
