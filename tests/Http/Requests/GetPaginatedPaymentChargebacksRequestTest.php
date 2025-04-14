<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentChargebacksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_chargebacks()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ]);

        $request = new GetPaginatedPaymentChargebacksRequest('tr_WDqYK6vllg');

        /** @var ChargebackCollection */
        $chargebacks = $client->send($request);

        $this->assertTrue($chargebacks->getResponse()->successful());
        $this->assertInstanceOf(ChargebackCollection::class, $chargebacks);
        $this->assertGreaterThan(0, $chargebacks->count());

        foreach ($chargebacks as $chargeback) {
            $this->assertInstanceOf(Chargeback::class, $chargeback);
            $this->assertEquals('chargeback', $chargeback->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_chargebacks()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentChargebacksRequest::class => MockResponse::ok('chargeback-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('chargeback-list'),
                MockResponse::ok('empty-list', 'chargebacks'),
            ),
        ]);

        $request = (new GetPaginatedPaymentChargebacksRequest('tr_WDqYK6vllg'))->useIterator();

        /** @var ChargebackCollection */
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
        $paymentId = 'tr_WDqYK6vllg';
        $request = new GetPaginatedPaymentChargebacksRequest($paymentId);

        $this->assertEquals("payments/{$paymentId}/chargebacks", $request->resolveResourcePath());
    }
}
