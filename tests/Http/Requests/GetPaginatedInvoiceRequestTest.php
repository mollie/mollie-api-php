<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Resources\InvoiceCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_invoices()
    {
        $client = new MockMollieClient([
            GetPaginatedInvoiceRequest::class => MockResponse::ok('invoice-list'),
        ]);

        $request = new GetPaginatedInvoiceRequest;

        /** @var InvoiceCollection */
        $invoices = $client->send($request);

        $this->assertTrue($invoices->getResponse()->successful());
        $this->assertInstanceOf(InvoiceCollection::class, $invoices);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedInvoiceRequest;

        $this->assertEquals('invoices', $request->resolveResourcePath());
    }
}
