<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $request = new GetPaginatedPaymentsRequest;

        /** @var PaymentCollection */
        $payments = $client->send($request);

        $this->assertTrue($payments->getResponse()->successful());

        // Assert response was properly handled
        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
            $this->assertEquals('payment', $payment->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_payments()
    {
        $client = MollieApiClient::fake([
            GetPaginatedPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payment-list'),
                MockResponse::ok('empty-list', 'payments'),
            ),
        ]);

        $request = (new GetPaginatedPaymentsRequest)->useIterator();

        /** @var LazyCollection */
        $payments = $client->send($request);
        $this->assertTrue($payments->getResponse()->successful());

        foreach ($payments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedPaymentsRequest;

        $this->assertEquals('payments', $request->resolveResourcePath());
    }
}
