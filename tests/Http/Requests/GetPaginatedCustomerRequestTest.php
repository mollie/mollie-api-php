<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\CustomerCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_customers()
    {
        $client = new MockClient([
            GetPaginatedCustomerRequest::class => new MockResponse(200, 'customer-list'),
        ]);

        $request = new GetPaginatedCustomerRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(CustomerCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedCustomerRequest;

        $this->assertEquals('customers', $request->resolveResourcePath());
    }
}
