<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;

class CustomerPaymentsEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for()
    {
        $client = new MockMollieClient([
            CreateCustomerPaymentRequest::class => MockResponse::created('payment'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Payment $payment */
        $payment = $client->customerPayments->createFor($customer, [
            'description' => 'Test payment',
            'amount' => new Money('10.00', 'EUR'),
            'redirectUrl' => 'https://example.org/redirect',
        ]);

        $this->assertPayment($payment);
    }

    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var PaymentCollection $payments */
        $payments = $client->customerPayments->pageFor($customer);

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertPayment($payment);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedCustomerPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payments'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        foreach ($client->customerPayments->iteratorFor($customer) as $payment) {
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
