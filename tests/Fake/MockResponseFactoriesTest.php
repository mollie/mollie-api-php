<?php

declare(strict_types=1);

namespace Tests\Fake;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Requests\GetMethodRequest;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Api\Types\PaymentStatus;
use PHPUnit\Framework\TestCase;

class MockResponseFactoriesTest extends TestCase
{
    /** @test */
    public function payment_factory_hydrates_to_payment_resource_with_defaults(): void
    {
        $client = new MockMollieClient([
            GetPaymentRequest::class => MockResponse::payment(
                id: 'tr_xxx',
                status: PaymentStatus::Paid,
                amount: new Money(currency: 'EUR', value: '10.00'),
                method: PaymentMethod::Ideal->value,
            ),
        ]);

        /** @var Payment $payment */
        $payment = $client->payments->get('tr_xxx');

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('tr_xxx', $payment->id);
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertSame('10.00', $payment->amount->value);
        $this->assertSame('EUR', $payment->amount->currency);
        $this->assertSame('ideal', $payment->method);
    }

    /** @test */
    public function payment_factory_fills_sensible_defaults_when_no_args_given(): void
    {
        $response = MockResponse::payment();

        $data = $response->json();

        $this->assertSame('payment', $data['resource']);
        $this->assertStringStartsWith('tr_', $data['id']);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('status', $data);
    }

    /** @test */
    public function customer_factory(): void
    {
        $client = new MockMollieClient([
            GetCustomerRequest::class => MockResponse::customer(id: 'cst_abc', name: 'Alice', email: 'a@b.c'),
        ]);

        /** @var Customer $customer */
        $customer = $client->customers->get('cst_abc');

        $this->assertSame('cst_abc', $customer->id);
        $this->assertSame('Alice', $customer->name);
        $this->assertSame('a@b.c', $customer->email);
    }

    /** @test */
    public function subscription_factory(): void
    {
        $client = new MockMollieClient([
            GetSubscriptionRequest::class => MockResponse::subscription(
                id: 'sub_abc',
                amount: new Money('EUR', '5.00'),
                customerId: 'cst_x',
            ),
        ]);

        /** @var Subscription $sub */
        $sub = $client->subscriptions->getForId('cst_x', 'sub_abc');

        $this->assertInstanceOf(Subscription::class, $sub);
        $this->assertSame('sub_abc', $sub->id);
        $this->assertSame('5.00', $sub->amount->value);
        $this->assertSame('cst_x', $sub->customerId);
    }

    /** @test */
    public function mandate_factory(): void
    {
        $client = new MockMollieClient([
            GetMandateRequest::class => MockResponse::mandate(id: 'mdt_abc', customerId: 'cst_x'),
        ]);

        /** @var Mandate $mandate */
        $mandate = $client->mandates->getForId('cst_x', 'mdt_abc');

        $this->assertSame('mdt_abc', $mandate->id);
        $this->assertSame('cst_x', $mandate->customerId);
    }

    /** @test */
    public function refund_factory(): void
    {
        $client = new MockMollieClient([
            GetPaymentRefundRequest::class => MockResponse::refund(
                id: 're_abc',
                paymentId: 'tr_xyz',
                amount: new Money('EUR', '1.00'),
            ),
        ]);

        /** @var Refund $refund */
        $refund = $client->paymentRefunds->getForId('tr_xyz', 're_abc');

        $this->assertSame('re_abc', $refund->id);
        $this->assertSame('1.00', $refund->amount->value);
    }

    /** @test */
    public function chargeback_factory(): void
    {
        $client = new MockMollieClient([
            GetPaymentChargebackRequest::class => MockResponse::chargeback(
                id: 'chb_abc',
                paymentId: 'tr_xyz',
                amount: new Money('EUR', '2.00'),
            ),
        ]);

        /** @var Chargeback $chargeback */
        $chargeback = $client->paymentChargebacks->getForId('tr_xyz', 'chb_abc');

        $this->assertSame('chb_abc', $chargeback->id);
    }

    /** @test */
    public function method_factory(): void
    {
        $client = new MockMollieClient([
            GetMethodRequest::class => MockResponse::method(id: 'ideal', description: 'iDEAL'),
        ]);

        /** @var Method $method */
        $method = $client->methods->get('ideal');

        $this->assertSame('ideal', $method->id);
        $this->assertSame('iDEAL', $method->description);
    }

    /** @test */
    public function payment_link_factory(): void
    {
        $client = new MockMollieClient([
            GetPaymentLinkRequest::class => MockResponse::paymentLink(
                id: 'pl_abc',
                description: 'Test link',
                amount: new Money('EUR', '24.95'),
            ),
        ]);

        /** @var PaymentLink $link */
        $link = $client->paymentLinks->get('pl_abc');

        $this->assertSame('pl_abc', $link->id);
        $this->assertSame('Test link', $link->description);
    }

    /** @test */
    public function invoice_factory(): void
    {
        $client = new MockMollieClient([
            GetInvoiceRequest::class => MockResponse::invoice(id: 'inv_abc', reference: '2026.0001'),
        ]);

        /** @var Invoice $invoice */
        $invoice = $client->invoices->get('inv_abc');

        $this->assertSame('inv_abc', $invoice->id);
        $this->assertSame('2026.0001', $invoice->reference);
    }

    /** @test */
    public function capture_factory(): void
    {
        $client = new MockMollieClient([
            GetPaymentCaptureRequest::class => MockResponse::capture(
                id: 'cpt_abc',
                paymentId: 'tr_xyz',
                amount: new Money('EUR', '3.50'),
            ),
        ]);

        /** @var Capture $capture */
        $capture = $client->paymentCaptures->getForId('tr_xyz', 'cpt_abc');

        $this->assertSame('cpt_abc', $capture->id);
    }

    /** @test */
    public function overrides_array_wins_over_typed_args(): void
    {
        $response = MockResponse::payment(id: 'tr_typed', overrides: ['id' => 'tr_override']);

        $this->assertSame('tr_override', $response->json()['id']);
    }

    /** @test */
    public function existing_ok_factory_still_works(): void
    {
        $response = MockResponse::ok(['resource' => 'payment', 'id' => 'tr_legacy']);

        $this->assertSame(['resource' => 'payment', 'id' => 'tr_legacy'], $response->json());
    }
}
