<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payments()
    {
        $client = new MockClient([
            GetPaginatedPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        $request = new GetPaginatedPaymentsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var PaymentCollection */
        $payments = $response->toResource();
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
        $client = new MockClient([
            GetPaginatedPaymentsRequest::class => new MockResponse(200, 'payment-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'payment-list'),
                new MockResponse(200, 'empty-list', 'payments'),
            ),
        ]);

        $request = (new GetPaginatedPaymentsRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $payments = $response->toResource();

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
