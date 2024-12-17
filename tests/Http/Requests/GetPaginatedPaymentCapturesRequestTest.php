<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class GetPaginatedPaymentCapturesRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_captures()
    {
        $client = new MockClient([
            GetPaginatedPaymentCapturesRequest::class => new MockResponse(200, 'capture-list'),
        ]);

        $request = new GetPaginatedPaymentCapturesRequest('tr_WDqYK6vllg');

        /** @var CaptureCollection */
        $captures = $client->send($request);

        $this->assertTrue($captures->getResponse()->successful());
        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertGreaterThan(0, $captures->count());

        foreach ($captures as $capture) {
            $this->assertInstanceOf(Capture::class, $capture);
            $this->assertEquals('capture', $capture->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_captures()
    {
        $client = new MockClient([
            GetPaginatedPaymentCapturesRequest::class => new MockResponse(200, 'capture-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'capture-list'),
                new MockResponse(200, 'empty-list', 'captures'),
            ),
        ]);

        $request = (new GetPaginatedPaymentCapturesRequest('tr_WDqYK6vllg'))->useIterator();

        /** @var CaptureCollection */
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
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaginatedPaymentCapturesRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}/captures", $request->resolveResourcePath());
    }
}
