<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Tests\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class ChargebackEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
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
        $client = new MockClient([
            GetPaginatedChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
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
