<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;
use PHPUnit\Framework\TestCase;

class SettlementsEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetSettlementRequest::class => MockResponse::ok('settlement'),
        ]);

        /** @var Settlement $settlement */
        $settlement = $client->settlements->get('stl_123');

        $this->assertSettlement($settlement);
    }

    /** @test */
    public function next()
    {
        $client = new MockMollieClient([
            GetSettlementRequest::class => MockResponse::ok('settlement'),
        ]);

        /** @var Settlement $settlement */
        $settlement = $client->settlements->next();

        $this->assertSettlement($settlement);
    }

    /** @test */
    public function open()
    {
        $client = new MockMollieClient([
            GetSettlementRequest::class => MockResponse::ok('settlement'),
        ]);

        /** @var Settlement $settlement */
        $settlement = $client->settlements->open();

        $this->assertSettlement($settlement);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementsRequest::class => MockResponse::ok('settlement-list'),
        ], true);

        /** @var SettlementCollection $settlements */
        $settlements = $client->settlements->page('stl_123', 50, ['year' => '2024', 'month' => '04']);

        $this->assertInstanceOf(SettlementCollection::class, $settlements);
        $this->assertGreaterThan(0, $settlements->count());
        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertEquals([
                'from' => 'stl_123',
                'limit' => 50,
                'balanceId' => null,
                'year' => '2024',
                'month' => '04',
                'currencies' => null,
            ], $pendingRequest->getRequest()->query()->all());

            return true;
        });

        foreach ($settlements as $settlement) {
            $this->assertSettlement($settlement);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementsRequest::class => MockResponse::ok('settlement-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'settlements'),
        ]);

        foreach ($client->settlements->iterator('stl_123', 50, ['reference' => 'test']) as $settlement) {
            $this->assertSettlement($settlement);
        }
    }

    protected function assertSettlement(Settlement $settlement)
    {
        $this->assertInstanceOf(Settlement::class, $settlement);
        $this->assertEquals('settlement', $settlement->resource);
        $this->assertNotEmpty($settlement->id);
        $this->assertNotEmpty($settlement->reference);
        $this->assertNotEmpty($settlement->settledAt);
        $this->assertNotEmpty($settlement->status);
        $this->assertNotEmpty($settlement->amount);
        $this->assertNotEmpty($settlement->_links);
    }
}
