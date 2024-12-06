<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Payload\CreatePaymentPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CustomerPaymentsEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for()
    {
        $client = new MockClient([
            CreateCustomerPaymentRequest::class => new MockResponse(201, 'payment'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Payment $payment */
        $payment = $client->customerPayments->createFor($customer, new CreatePaymentPayload(
            'Test payment',
            new Money('10.00', 'EUR'),
            'https://example.org/redirect',
        ));

        $this->assertPayment($payment);
    }

    /** @test */
    public function page_for()
    {
        $client = new MockClient([
            GetPaginatedCustomerPaymentsRequest::class => new MockResponse(200, 'payment-list'),
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
        $client = new MockClient([
            GetPaginatedCustomerPaymentsRequest::class => new MockResponse(200, 'payment-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'payments'),
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
