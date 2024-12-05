<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\LazyCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedSettlementRefundsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlement_refunds()
    {
        $client = new MockClient([
            GetPaginatedSettlementRefundsRequest::class => new MockResponse(200, 'refund-list'),
        ]);

        $request = new GetPaginatedSettlementRefundsRequest('stl_jDk30akdN');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var RefundCollection */
        $refunds = $response->toResource();
        // Assert response was properly handled
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
        $client = new MockClient([
            GetPaginatedSettlementRefundsRequest::class => new MockResponse(200, 'refund-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'refund-list'),
                new MockResponse(200, 'empty-list', 'refunds'),
            ),
        ]);

        $request = (new GetPaginatedSettlementRefundsRequest('stl_jDk30akdN'))->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $refunds = $response->toResource();

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
