<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\GetPaginatedCustomerPaymentsQuery;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\PaymentCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedCustomerPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_customer_payments()
    {
        $client = new MockClient([
            GetPaginatedCustomerPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedCustomerPaymentsRequest($customerId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(PaymentCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedCustomerPaymentsRequest($customerId);

        $this->assertEquals("customers/{$customerId}/payments", $request->resolveResourcePath());
    }
}
