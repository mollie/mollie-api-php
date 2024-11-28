<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaginatedSettlementChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class SettlementChargebackEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for_test()
    {
        $client = new MockClient([
            GetPaginatedSettlementChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        /** @var ChargebackCollection $chargebacks */
        $chargebacks = $client->settlementChargebacks->pageFor($settlement);

        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertGreaterThan(0, $chargebacks->count());

        foreach ($chargebacks as $chargeback) {
            $this->assertChargeback($chargeback);
        }
    }

    /** @test */
    public function iterator_for_test()
    {
        $client = new MockClient([
            GetPaginatedSettlementChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        foreach ($client->settlementChargebacks->iteratorFor($settlement) as $chargeback) {
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
        $this->assertNotEmpty($chargeback->_links);
    }
}
