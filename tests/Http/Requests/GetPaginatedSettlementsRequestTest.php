<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedSettlementsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlements()
    {
        $client = new MockClient([
            GetPaginatedSettlementsRequest::class => new MockResponse(200, 'settlement-list'),
        ]);

        $request = new GetPaginatedSettlementsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var SettlementCollection */
        $settlements = $response->toResource();
        // Assert response was properly handled
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
        $client = new MockClient([
            GetPaginatedSettlementsRequest::class => new MockResponse(200, 'settlement-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'settlement-list'),
                new MockResponse(200, 'empty-list', 'settlements'),
            ),
        ]);

        $request = (new GetPaginatedSettlementsRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $settlements = $response->toResource();

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