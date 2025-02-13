<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinkPaymentsRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentLinkPaymentsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payment_link_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $request = new GetPaginatedPaymentLinkPaymentsRequest('pl_4Y0eZitmBnQ5jsBYZIBw');

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
    public function it_can_iterate_over_payment_link_payments()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payment-list'),
                MockResponse::ok('empty-list', 'payments'),
            ),
        ]);

        $request = (new GetPaginatedPaymentLinkPaymentsRequest('pl_4Y0eZitmBnQ5jsBYZIBw'))->useIterator();

        /** @var PaymentCollection */
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
        $paymentLinkId = 'pl_4Y0eZitmBnQ5jsBYZIBw';
        $request = new GetPaginatedPaymentLinkPaymentsRequest($paymentLinkId);

        $this->assertEquals("payment-links/{$paymentLinkId}/payments", $request->resolveResourcePath());
    }
}
