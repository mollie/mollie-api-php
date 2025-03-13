<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;

class SettlementRefundEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        $settlement = new Settlement($client);
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
        $client = new MockMollieClient([
            GetPaginatedSettlementRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'refunds'),
        ]);

        $settlement = new Settlement($client);
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
