<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedRefundsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_refunds()
    {
        $client = new MockMollieClient([
            GetPaginatedRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        $request = new GetPaginatedRefundsRequest;

        /** @var RefundCollection */
        $refunds = $client->send($request);

        $this->assertTrue($refunds->getResponse()->successful());

        foreach ($refunds as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
            $this->assertEquals('refund', $refund->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_refunds()
    {
        $client = new MockMollieClient([
            GetPaginatedRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('refund-list'),
                MockResponse::ok('empty-list', 'refunds'),
            ),
        ]);

        $request = (new GetPaginatedRefundsRequest)->useIterator();

        /** @var RefundCollection */
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
        $request = new GetPaginatedRefundsRequest;

        $this->assertEquals('refunds', $request->resolveResourcePath());
    }
}
