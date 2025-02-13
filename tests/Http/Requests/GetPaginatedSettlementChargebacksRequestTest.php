<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\LazyCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementChargebacksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlement_chargebacks()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ]);

        $request = new GetPaginatedSettlementChargebacksRequest('stl_jDk30akdN');

        /** @var LazyCollection */
        $chargebacks = $client->send($request);

        $this->assertTrue($chargebacks->getResponse()->successful());

        foreach ($chargebacks as $chargeback) {
            $this->assertInstanceOf(Chargeback::class, $chargeback);
            $this->assertEquals('chargeback', $chargeback->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_settlement_chargebacks()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementChargebacksRequest::class => MockResponse::ok('chargeback-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('chargeback-list'),
                MockResponse::ok('empty-list', 'chargebacks'),
            ),
        ]);

        $request = (new GetPaginatedSettlementChargebacksRequest('stl_jDk30akdN'))->useIterator();

        /** @var LazyCollection */
        $chargebacks = $client->send($request);
        $this->assertTrue($chargebacks->getResponse()->successful());

        foreach ($chargebacks as $chargeback) {
            $this->assertInstanceOf(Chargeback::class, $chargeback);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $settlementId = 'stl_jDk30akdN';
        $request = new GetPaginatedSettlementChargebacksRequest($settlementId);

        $this->assertEquals("settlements/{$settlementId}/chargebacks", $request->resolveResourcePath());
    }
}
