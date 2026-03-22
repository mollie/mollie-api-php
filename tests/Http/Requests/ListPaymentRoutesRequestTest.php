<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ListPaymentRoutesRequest;
use Mollie\Api\Resources\Route;
use Mollie\Api\Resources\RouteCollection;
use PHPUnit\Framework\TestCase;

class ListPaymentRoutesRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_payment_routes()
    {
        $client = new MockMollieClient([
            ListPaymentRoutesRequest::class => MockResponse::ok('route-list'),
        ]);

        $request = new ListPaymentRoutesRequest('tr_12345');

        /** @var RouteCollection */
        $routes = $client->send($request);

        $this->assertTrue($routes->getResponse()->successful());
        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertGreaterThan(0, $routes->count());

        foreach ($routes as $route) {
            $this->assertInstanceOf(Route::class, $route);
            $this->assertEquals('route', $route->resource);
        }
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new ListPaymentRoutesRequest('tr_12345');

        $this->assertEquals('payments/tr_12345/routes', $request->resolveResourcePath());
    }
}
