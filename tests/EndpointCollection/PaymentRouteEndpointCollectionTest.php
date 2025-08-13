<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateDelayedPaymentRouteRequest;
use Mollie\Api\Http\Requests\ListPaymentRoutesRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use Mollie\Api\Resources\Route;
use Mollie\Api\Resources\RouteCollection;
use PHPUnit\Framework\TestCase;

class PaymentRouteEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_delayed_route_for_id()
    {
        $client = new MockMollieClient([
            CreateDelayedPaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        $amount = ['value' => '10.00', 'currency' => 'EUR'];
        $destination = ['type' => 'organization', 'organizationId' => 'org_12345'];

        /** @var Route $route */
        $route = $client->paymentRoutes->createForId('tr_7UhSN1zuXS', $amount, $destination, '2025-01-01');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->resource);
        $this->assertNotEmpty($route->id);
        $this->assertNotEmpty($route->amount);
        $this->assertNotEmpty($route->destination);
        $this->assertNotEmpty($route->releaseDate);
    }
    /** @test */
    public function create_delayed_route_for_id_without_release_date()
    {
        $client = new MockMollieClient([
            CreateDelayedPaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        $amount = ['value' => '10.00', 'currency' => 'EUR'];
        $destination = ['type' => 'organization', 'organizationId' => 'org_12345'];

        /** @var Route $route */
        $route = $client->paymentRoutes->createForId('tr_7UhSN1zuXS', $amount, $destination);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->resource);
        $this->assertNotEmpty($route->id);
        $this->assertNotEmpty($route->amount);
        $this->assertNotEmpty($route->destination);
    }

    /** @test */
    public function list_for_id()
    {
        $client = new MockMollieClient([
            ListPaymentRoutesRequest::class => MockResponse::ok('route-list'),
        ]);

        /** @var RouteCollection $routes */
        $routes = $client->paymentRoutes->listForId('tr_7UhSN1zuXS');

        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertGreaterThan(0, $routes->count());

        foreach ($routes as $route) {
            $this->assertInstanceOf(Route::class, $route);
            $this->assertEquals('route', $route->resource);
        }
    }

    /** @test */
    public function update_release_date_for()
    {
        $client = new MockMollieClient([
            UpdatePaymentRouteRequest::class => MockResponse::ok('route'),
        ]);

        /** @var Route $route */
        $route = $client->paymentRoutes->updateReleaseDateForId('tr_7UhSN1zuXS', 'rt_abc123', '2024-01-01');

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('route', $route->resource);
        $this->assertNotEmpty($route->id);
        $this->assertNotEmpty($route->amount);
        $this->assertNotEmpty($route->destination);
        $this->assertNotEmpty($route->releaseDate);
    }
}
