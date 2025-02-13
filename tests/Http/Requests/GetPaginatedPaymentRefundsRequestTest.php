<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentRefundsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_refunds()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        $request = new GetPaginatedPaymentRefundsRequest('tr_WDqYK6vllg');

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
            GetPaginatedPaymentRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('refund-list'),
                MockResponse::ok('empty-list', 'refunds'),
            ),
        ]);

        $request = (new GetPaginatedPaymentRefundsRequest('tr_WDqYK6vllg'))->useIterator();

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
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaginatedPaymentRefundsRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}/refunds", $request->resolveResourcePath());
    }
}
