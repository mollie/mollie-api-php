<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRouteRequest;
use Mollie\Api\Resources\Route;
use PHPUnit\Framework\TestCase;

class GetPaymentRouteRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_route()
    {
        $client = new MockMollieClient([
            GetPaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        $request = new GetPaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP');

        /** @var Route */
        $route = $client->send($request);

        $this->assertTrue($route->getResponse()->successful());
        $this->assertInstanceOf(Route::class, $route);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP');

        $this->assertEquals(
            'payments/tr_WDqYK6vllg/routes/rt_H2wvxEyQcP',
            $request->resolveResourcePath()
        );
    }
}
