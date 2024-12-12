<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\CreateRefundPaymentPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\UpdatePaymentPayload;
use Mollie\Api\Http\Requests\CancelPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class PaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreatePaymentRequest::class => new MockResponse(201, 'payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->create(new CreatePaymentPayload(
            'Test payment',
            new Money('10.00', 'EUR'),
            'https://example.org/redirect',
            'https://example.org/webhook',
        ));

        $this->assertPayment($payment);
    }

    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetPaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->get('tr_WDqYK6vllg');

        $this->assertPayment($payment);
    }

    /** @test */
    public function update()
    {
        $client = new MockClient([
            UpdatePaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->update('tr_WDqYK6vllg', new UpdatePaymentPayload(
            'Updated description',
            'https://example.org/updated-redirect',
        ));

        $this->assertPayment($payment);
    }

    /** @test */
    public function cancel()
    {
        $client = new MockClient([
            CancelPaymentRequest::class => new MockResponse(204),
        ]);

        $payment = $client->payments->cancel('tr_WDqYK6vllg');

        $this->assertTrue($payment->getResponse()->successful());
    }

    /** @test */
    public function refund()
    {
        $client = new MockClient([
            CreatePaymentRefundRequest::class => new MockResponse(201, 'refund'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_WDqYK6vllg';

        /** @var Refund $refund */
        $refund = $client->payments->refund($payment, new CreateRefundPaymentPayload(
            'Test refund',
            new Money('5.95', 'EUR'),
        ));

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
        $this->assertNotEmpty($refund->id);
        $this->assertEquals('5.95', $refund->amount->value);
        $this->assertEquals('EUR', $refund->amount->currency);
    }

    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        /** @var PaymentCollection $payments */
        $payments = $client->payments->page();

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());
        $this->assertGreaterThan(0, count($payments));

        foreach ($payments as $payment) {
            $this->assertPayment($payment);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockClient([
            GetPaginatedPaymentsRequest::class => new MockResponse(200, 'payment-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'payments'),
        ]);

        foreach ($client->payments->iterator() as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
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
    }
}
