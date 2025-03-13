<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlements()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementsRequest::class => MockResponse::ok('settlement-list'),
        ]);

        $request = new GetPaginatedSettlementsRequest;

        /** @var SettlementCollection */
        $settlements = $client->send($request);

        $this->assertTrue($settlements->getResponse()->successful());
        $this->assertInstanceOf(SettlementCollection::class, $settlements);
        $this->assertGreaterThan(0, $settlements->count());

        foreach ($settlements as $settlement) {
            $this->assertInstanceOf(Settlement::class, $settlement);
            $this->assertEquals('settlement', $settlement->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_settlements()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementsRequest::class => MockResponse::ok('settlement-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('settlement-list'),
                MockResponse::ok('empty-list', 'settlements'),
            ),
        ]);

        $request = (new GetPaginatedSettlementsRequest)->useIterator();

        /** @var LazyCollection */
        $settlements = $client->send($request);
        $this->assertTrue($settlements->getResponse()->successful());

        foreach ($settlements as $settlement) {
            $this->assertInstanceOf(Settlement::class, $settlement);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedSettlementsRequest;

        $this->assertEquals('settlements', $request->resolveResourcePath());
    }
}
