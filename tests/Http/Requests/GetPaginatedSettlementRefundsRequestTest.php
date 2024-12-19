<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementRefundsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlement_refunds()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        $request = new GetPaginatedSettlementRefundsRequest('stl_jDk30akdN');

        /** @var RefundCollection */
        $refunds = $client->send($request);

        $this->assertTrue($refunds->getResponse()->successful());

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertGreaterThan(0, $refunds->count());

        foreach ($refunds as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
            $this->assertEquals('refund', $refund->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_settlement_refunds()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('refund-list'),
                MockResponse::ok('empty-list', 'refunds'),
            ),
        ]);

        $request = (new GetPaginatedSettlementRefundsRequest('stl_jDk30akdN'))->useIterator();

        /** @var LazyCollection */
        $refunds = $client->send($request);
        $this->assertTrue($refunds->getResponse()->successful());

        foreach ($refunds as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $settlementId = 'stl_jDk30akdN';
        $request = new GetPaginatedSettlementRefundsRequest($settlementId);

        $this->assertEquals("settlements/{$settlementId}/refunds", $request->resolveResourcePath());
    }
}
