<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionPaymentsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSubscriptionPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_subscription_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $request = new GetPaginatedSubscriptionPaymentsRequest('cst_kEn1PlbGa', 'sub_rVKGtNd6s3');

        /** @var PaymentCollection */
        $payments = $client->send($request);

        $this->assertTrue($payments->getResponse()->successful());
        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
            $this->assertEquals('payment', $payment->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_subscription_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payment-list'),
                MockResponse::ok('empty-list', 'payments'),
            ),
        ]);

        $request = (new GetPaginatedSubscriptionPaymentsRequest('cst_kEn1PlbGa', 'sub_rVKGtNd6s3'))->useIterator();

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
        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';
        $request = new GetPaginatedSubscriptionPaymentsRequest($customerId, $subscriptionId);

        $this->assertEquals("customers/{$customerId}/subscriptions/{$subscriptionId}/payments", $request->resolveResourcePath());
    }
}
