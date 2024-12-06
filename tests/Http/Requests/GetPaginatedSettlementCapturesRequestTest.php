<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedSettlementCapturesRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlement_captures()
    {
        $client = new MockClient([
            GetPaginatedSettlementCapturesRequest::class => new MockResponse(200, 'capture-list'),
        ]);

        $request = new GetPaginatedSettlementCapturesRequest('stl_jDk30akdN');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var CaptureCollection */
        $captures = $response->toResource();
        // Assert response was properly handled
        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertGreaterThan(0, $captures->count());

        foreach ($captures as $capture) {
            $this->assertInstanceOf(Capture::class, $capture);
            $this->assertEquals('capture', $capture->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_settlement_captures()
    {
        $client = new MockClient([
            GetPaginatedSettlementCapturesRequest::class => new MockResponse(200, 'capture-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'capture-list'),
                new MockResponse(200, 'empty-list', 'captures'),
            ),
        ]);

        $request = (new GetPaginatedSettlementCapturesRequest('stl_jDk30akdN'))->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $captures = $response->toResource();

        foreach ($captures as $capture) {
            $this->assertInstanceOf(Capture::class, $capture);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $settlementId = 'stl_jDk30akdN';
        $request = new GetPaginatedSettlementCapturesRequest($settlementId);

        $this->assertEquals("settlements/{$settlementId}/captures", $request->resolveResourcePath());
    }
}
