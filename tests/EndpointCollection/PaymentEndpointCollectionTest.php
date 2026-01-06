<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CancelPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\ReleasePaymentAuthorizationRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;
use PHPUnit\Framework\TestCase;

class PaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreatePaymentRequest::class => MockResponse::created('payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->create([
            'description' => 'Test payment',
            'amount' => new Money('10.00', 'EUR'),
            'redirectUrl' => 'https://example.org/redirect',
            'webhookUrl' => 'https://example.org/webhook',
        ]);

        $this->assertPayment($payment);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->get('tr_WDqYK6vllg');

        $this->assertPayment($payment);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdatePaymentRequest::class => MockResponse::ok('payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->update('tr_WDqYK6vllg', [
            'description' => 'Updated description',
            'redirectUrl' => 'https://example.org/updated-redirect',
        ]);

        $this->assertPayment($payment);
    }

    /** @test */
    public function cancel()
    {
        $client = new MockMollieClient([
            CancelPaymentRequest::class => MockResponse::ok('payment'),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->cancel('tr_WDqYK6vllg');

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function refund()
    {
        $client = new MockMollieClient([
            CreatePaymentRefundRequest::class => MockResponse::created('refund'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_WDqYK6vllg';

        /** @var Refund $refund */
        $refund = $client->payments->refund($payment, [
            'description' => 'Test refund',
            'amount' => new Money('5.95', 'EUR'),
        ]);

        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
        $this->assertNotEmpty($refund->id);
        $this->assertEquals('5.95', $refund->amount->value);
        $this->assertEquals('EUR', $refund->amount->currency);
    }

    /** @test */
    public function release_authorization()
    {
        $client = new MockMollieClient([
            ReleasePaymentAuthorizationRequest::class => MockResponse::noContent(),
        ]);

        $paymentId = 'tr_WDqYK6vllg';

        /** @var Response $response */
        $response = $client->payments->releaseAuthorization($paymentId);

        $this->assertTrue($response->successful());
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentsRequest::class => MockResponse::ok('payment-list'),
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
        $client = new MockMollieClient([
            GetPaginatedPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payments'),
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
