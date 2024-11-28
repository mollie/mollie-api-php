<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class PaymentChargebackEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get_for_test()
    {
        $client = new MockClient([
            GetPaymentChargebackRequest::class => new MockResponse(200, 'chargeback'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Chargeback $chargeback */
        $chargeback = $client->paymentChargebacks->getFor($payment, 'chb_n9z0tp');

        $this->assertChargeback($chargeback);
    }

    /** @test */
    public function page_for_test()
    {
        $client = new MockClient([
            GetPaginatedPaymentChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var ChargebackCollection $chargebacks */
        $chargebacks = $client->paymentChargebacks->pageFor($payment);

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertGreaterThan(0, $chargebacks->count());
        $this->assertGreaterThan(0, count($chargebacks));

        foreach ($chargebacks as $chargeback) {
            $this->assertChargeback($chargeback);
        }
    }

    /** @test */
    public function iterator_for_test()
    {
        $client = new MockClient([
            GetPaginatedPaymentChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        foreach ($client->paymentChargebacks->iteratorFor($payment) as $chargeback) {
            $this->assertInstanceOf(Chargeback::class, $chargeback);
            $this->assertChargeback($chargeback);
        }
    }

    protected function assertChargeback(Chargeback $chargeback)
    {
        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals('chargeback', $chargeback->resource);
        $this->assertNotEmpty($chargeback->id);
        $this->assertNotEmpty($chargeback->amount);
        $this->assertNotEmpty($chargeback->settlementAmount);
        $this->assertNotEmpty($chargeback->createdAt);
        $this->assertEquals('tr_7UhSN1zuXS', $chargeback->paymentId);
        $this->assertNotEmpty($chargeback->_links);
    }
}
