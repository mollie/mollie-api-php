<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\InvoiceCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaginatedInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_invoices()
    {
        $client = new MockClient([
            GetPaginatedInvoiceRequest::class => new MockResponse(200, 'invoice-list'),
        ]);

        $request = new GetPaginatedInvoiceRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(InvoiceCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedInvoiceRequest;

        $this->assertEquals('invoices', $request->resolveResourcePath());
    }
}
