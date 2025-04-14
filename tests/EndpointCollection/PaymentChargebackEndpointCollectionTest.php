<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use PHPUnit\Framework\TestCase;

class PaymentChargebackEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get_for_id()
    {
        $client = new MockMollieClient([
            GetPaymentChargebackRequest::class => MockResponse::ok('chargeback'),
        ]);

        /** @var Chargeback $chargeback */
        $chargeback = $client->paymentChargebacks->getForId('tr_7UhSN1zuXS', 'chb_n9z0tp');

        $this->assertChargeback($chargeback);
    }

    /** @test */
    public function page_for_id()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ]);

        /** @var ChargebackCollection $chargebacks */
        $chargebacks = $client->paymentChargebacks->pageForId('tr_7UhSN1zuXS');

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertGreaterThan(0, $chargebacks->count());
        $this->assertGreaterThan(0, count($chargebacks));

        foreach ($chargebacks as $chargeback) {
            $this->assertChargeback($chargeback);
        }
    }

    /** @test */
    public function iterator_for_id()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentChargebacksRequest::class => MockResponse::ok('chargeback-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'chargebacks'),
        ]);

        foreach ($client->paymentChargebacks->iteratorForId('tr_7UhSN1zuXS') as $chargeback) {
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
        $this->assertStringStartsWith('tr_', $chargeback->paymentId);
        $this->assertNotEmpty($chargeback->_links);
    }
}
