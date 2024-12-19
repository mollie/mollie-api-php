<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentLinksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_payment_links()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentLinksRequest::class => MockResponse::ok('payment-link-list'),
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
        $client = new MockMollieClient([
            GetPaginatedPaymentLinksRequest::class => MockResponse::ok('payment-link-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('payment-link-list'),
                MockResponse::ok('empty-list', 'payment_links'),
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
