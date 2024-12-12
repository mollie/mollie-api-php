<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementPaymentsRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Settlement;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class SettlementPaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockClient([
            GetPaginatedSettlementPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        /** @var PaymentCollection $payments */
        $payments = $client->settlementPayments->pageFor($settlement);

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
            GetPaginatedSettlementPaymentsRequest::class => new MockResponse(200, 'payment-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'payments'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        foreach ($client->settlementPayments->iteratorFor($settlement) as $payment) {
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
