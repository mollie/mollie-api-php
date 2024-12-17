<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class GetPaginatedPaymentLinksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payment_links()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinksRequest::class => new MockResponse(200, 'payment-link-list'),
        ]);

        $request = new GetPaginatedPaymentLinksRequest;

        /** @var PaymentLinkCollection */
        $paymentLinks = $client->send($request);

        $this->assertTrue($paymentLinks->getResponse()->successful());

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

        /** @var PaymentLinkCollection */
        $paymentLinks = $client->send($request);

        $this->assertTrue($paymentLinks->getResponse()->successful());

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
