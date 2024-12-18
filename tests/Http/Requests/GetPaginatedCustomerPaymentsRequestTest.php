<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

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

        /** @var PaymentCollection */
        $payments = $client->send($request);

        $this->assertTrue($payments->getResponse()->successful());
        $this->assertInstanceOf(PaymentCollection::class, $payments);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedCustomerPaymentsRequest($customerId);

        $this->assertEquals("customers/{$customerId}/payments", $request->resolveResourcePath());
    }
}
