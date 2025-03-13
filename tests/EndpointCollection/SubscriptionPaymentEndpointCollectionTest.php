<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionPaymentsRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionPaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $subscription = new Subscription(
            $client,
        );
        $subscription->id = 'sub_rVKGtNd6s3';
        $subscription->customerId = 'cust_kEn1PlbGa';

        /** @var PaymentCollection $payments */
        $payments = $client->subscriptionPayments->pageFor($subscription);

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
            GetPaginatedSubscriptionPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payments'),
        ]);

        $subscription = new Subscription(
            $client,
        );
        $subscription->id = 'sub_rVKGtNd6s3';
        $subscription->customerId = 'cust_kEn1PlbGa';

        foreach ($client->subscriptionPayments->iteratorFor($subscription) as $payment) {
            $this->assertPayment($payment);
        }
    }

    protected function assertPayment(Payment $payment)
    {
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertNotEmpty($payment->id);
        $this->assertNotEmpty($payment->amount);
        $this->assertNotEmpty($payment->description);
        $this->assertNotEmpty($payment->createdAt);
        $this->assertNotEmpty($payment->status);
        $this->assertNotEmpty($payment->profileId);
        $this->assertNotEmpty($payment->_links);
    }
}
