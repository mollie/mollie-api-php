<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class SettlementRefundEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockClient([
            GetPaginatedSettlementRefundsRequest::class => new MockResponse(200, 'refund-list'),
        ]);

        $settlement = new Settlement(
            $client,
            $this->createMock(Response::class)
        );
        $settlement->id = 'stl_jDk30akdN';

        /** @var RefundCollection $refunds */
        $refunds = $client->settlementRefunds->pageFor($settlement);

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertGreaterThan(0, $refunds->count());

        foreach ($refunds as $refund) {
            $this->assertRefund($refund);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockClient([
            GetPaginatedSettlementRefundsRequest::class => new MockResponse(200, 'refund-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'refunds'),
        ]);

        $settlement = new Settlement(
            $client,
            $this->createMock(Response::class)
        );
        $settlement->id = 'stl_jDk30akdN';

        foreach ($client->settlementRefunds->iteratorFor($settlement) as $refund) {
            $this->assertRefund($refund);
        }
    }

    protected function assertRefund(Refund $refund)
    {
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
        $this->assertNotEmpty($refund->id);
        $this->assertNotEmpty($refund->amount);
        $this->assertNotEmpty($refund->status);
        $this->assertNotEmpty($refund->createdAt);
        $this->assertNotEmpty($refund->paymentId);
        $this->assertNotEmpty($refund->_links);
    }
}
