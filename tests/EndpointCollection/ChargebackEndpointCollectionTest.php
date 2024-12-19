<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use PHPUnit\Framework\TestCase;

class ChargebackEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ]);

        /** @var ChargebackCollection $chargebacks */
        $chargebacks = $client->chargebacks->page();

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertGreaterThan(0, $chargebacks->count());

        foreach ($chargebacks as $chargeback) {
            $this->assertChargeback($chargeback);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedChargebacksRequest::class => MockResponse::ok('chargeback-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'chargebacks'),
        ]);

        foreach ($client->chargebacks->iterator() as $chargeback) {
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
        $this->assertNotEmpty($chargeback->paymentId);
    }
}
