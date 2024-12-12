<?php

namespace Tests\Http\Requests;

use DateTime;
use Mollie\Api\Http\Data\UpdatePaymentRoutePayload;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Route;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdatePaymentRouteRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment_route()
    {
        $client = new MockClient([
            UpdatePaymentRouteRequest::class => new MockResponse(200, 'route'),
        ]);

        $request = new UpdatePaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP', new UpdatePaymentRoutePayload(
            new DateTime('2024-01-01'),
        ));

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Route */
        $route = $response->toResource();

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentRouteRequest('tr_WDqYK6vllg', 'rt_H2wvxEyQcP', new UpdatePaymentRoutePayload(
            new DateTime('2024-01-01'),
        ));

        $this->assertEquals('payments/tr_WDqYK6vllg/routes/rt_H2wvxEyQcP', $request->resolveResourcePath());
    }
}
