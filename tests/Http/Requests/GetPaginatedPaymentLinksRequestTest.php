<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedPaymentLinksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payment_links()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinksRequest::class => new MockResponse(200, 'payment-link-list'),
        ]);

        $request = new GetPaginatedPaymentLinksRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var PaymentLinkCollection */
        $paymentLinks = $response->toResource();
        // Assert response was properly handled
        $this->assertInstanceOf(PaymentLinkCollection::class, $paymentLinks);
        $this->assertGreaterThan(0, $paymentLinks->count());

        foreach ($paymentLinks as $paymentLink) {
            $this->assertInstanceOf(PaymentLink::class, $paymentLink);
            $this->assertEquals('payment-link', $paymentLink->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_payment_links()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinksRequest::class => new MockResponse(200, 'payment-link-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'payment-link-list'),
                new MockResponse(200, 'empty-list', 'payment_links'),
            ),
        ]);

        $request = (new GetPaginatedPaymentLinksRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $paymentLinks = $response->toResource();

        foreach ($paymentLinks as $paymentLink) {
            $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedPaymentLinksRequest;

        $this->assertEquals('payment-links', $request->resolveResourcePath());
    }
}
