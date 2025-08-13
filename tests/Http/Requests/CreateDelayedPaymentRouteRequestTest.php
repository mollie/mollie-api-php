<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateDelayedPaymentRouteRequest;
use Mollie\Api\Resources\Route;
use PHPUnit\Framework\TestCase;

class CreateDelayedPaymentRouteRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_delayed_route()
    {
        $client = new MockMollieClient([
            CreateDelayedPaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        $amount = new Money('10.00', 'EUR');
        $destination = ['type' => 'organization', 'organizationId' => 'org_12345'];

        $request = new CreateDelayedPaymentRouteRequest('tr_12345', $amount, $destination);

        /** @var Route */
        $route = $client->send($request);

        $this->assertTrue($route->getResponse()->successful());
        $this->assertInstanceOf(Route::class, $route);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $amount = new Money('10.00', 'EUR');
        $destination = ['type' => 'organization', 'organizationId' => 'org_12345'];

        $request = new CreateDelayedPaymentRouteRequest('tr_12345', $amount, $destination);

        $this->assertEquals('payments/tr_12345/routes', $request->resolveResourcePath());
    }
}
