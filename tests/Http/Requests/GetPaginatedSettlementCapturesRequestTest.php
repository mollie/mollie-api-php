<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementCapturesRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_settlement_captures()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementCapturesRequest::class => MockResponse::ok('capture-list'),
        ]);

        $request = new GetPaginatedSettlementCapturesRequest('stl_jDk30akdN');

        /** @var CaptureCollection */
        $captures = $client->send($request);

        $this->assertTrue($captures->getResponse()->successful());

        foreach ($captures as $capture) {
            $this->assertInstanceOf(Capture::class, $capture);
            $this->assertEquals('capture', $capture->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_settlement_captures()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementCapturesRequest::class => MockResponse::ok('capture-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('capture-list'),
                MockResponse::ok('empty-list', 'captures'),
            ),
        ]);

        $request = (new GetPaginatedSettlementCapturesRequest('stl_jDk30akdN'))->useIterator();

        /** @var LazyCollection */
        $captures = $client->send($request);
        $this->assertTrue($captures->getResponse()->successful());

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
