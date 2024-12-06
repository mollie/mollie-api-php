<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedPaymentChargebacksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_chargebacks()
    {
        $client = new MockClient([
            GetPaginatedPaymentChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
        ]);

        $request = new GetPaginatedPaymentChargebacksRequest('tr_WDqYK6vllg');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var ChargebackCollection */
        $chargebacks = $response->toResource();
        // Assert response was properly handled
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
        $client = new MockClient([
            GetPaginatedPaymentChargebacksRequest::class => new MockResponse(200, 'chargeback-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'chargeback-list'),
                new MockResponse(200, 'empty-list', 'chargebacks'),
            ),
        ]);

        $request = (new GetPaginatedPaymentChargebacksRequest('tr_WDqYK6vllg'))->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $chargebacks = $response->toResource();

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