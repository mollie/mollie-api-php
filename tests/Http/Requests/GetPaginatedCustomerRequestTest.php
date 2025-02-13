<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Resources\CustomerCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedCustomerRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_customers()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerRequest::class => MockResponse::ok('customer-list'),
        ]);

        $request = new GetPaginatedCustomerRequest;

        /** @var CustomerCollection */
        $customers = $client->send($request);

        $this->assertTrue($customers->getResponse()->successful());
        $this->assertInstanceOf(CustomerCollection::class, $customers);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedCustomerRequest;

        $this->assertEquals('customers', $request->resolveResourcePath());
    }
}
