<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\ChargebackCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedChargebacksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_chargebacks()
    {
        $client = new MockClient([
            GetPaginatedChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $request = new GetPaginatedChargebacksRequest();

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(ChargebackCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedChargebacksRequest();

        $this->assertEquals('chargebacks', $request->resolveResourcePath());
    }
}
