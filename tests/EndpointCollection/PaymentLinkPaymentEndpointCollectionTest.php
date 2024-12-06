<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinkPaymentsRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class PaymentLinkPaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        /** @var PaymentCollection $payments */
        $payments = $client->paymentLinkPayments->pageForId('pl_4Y0eZitmBnQ6IDoMqZQKh');

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertPayment($payment);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => new MockResponse(200, 'payment-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'payments'),
        ]);

        foreach ($client->paymentLinkPayments->iteratorForId('pl_4Y0eZitmBnQ6IDoMqZQKh') as $payment) {
            $this->assertPayment($payment);
        }
    }

    protected function assertPayment(Payment $payment)
    {
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertNotEmpty($payment->id);
        $this->assertNotEmpty($payment->mode);
        $this->assertNotEmpty($payment->createdAt);
        $this->assertNotEmpty($payment->status);
        $this->assertNotEmpty($payment->amount);
        $this->assertNotEmpty($payment->description);
        $this->assertNotEmpty($payment->method);
        $this->assertNotEmpty($payment->metadata);
        $this->assertNotEmpty($payment->profileId);
        $this->assertNotEmpty($payment->_links);
    }
}
