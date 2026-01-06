<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedCustomerPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_customer_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedCustomerPaymentsRequest($customerId);

        /** @var PaymentCollection */
        $payments = $client->send($request);

        $this->assertTrue($payments->getResponse()->successful());

        // Assert response was properly handled
        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
        }
    }

    /** @test */
    public function it_can_iterate_over_customer_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payment-list'),
                MockResponse::ok('empty-list', 'payments'),
            ),
        ]);

        $request = (new GetPaginatedCustomerPaymentsRequest('cst_kEn1PlbGa'))->useIterator();

        /** @var LazyCollection */
        $payments = $client->send($request);
        $this->assertTrue($payments->getResponse()->successful());

        foreach ($payments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
        }
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedCustomerPaymentsRequest($customerId);

        $this->assertEquals("customers/{$customerId}/payments", $request->resolveResourcePath());
    }
}
