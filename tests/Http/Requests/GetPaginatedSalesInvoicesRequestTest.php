<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Resources\SalesInvoiceCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;
use Tests\TestCase;

class GetPaginatedSalesInvoicesRequestTest extends TestCase
{
    /** @test */
    public function it_gets_paginated_sales_invoices()
    {
        $client = new MockClient([
            GetPaginatedSalesInvoicesRequest::class => new MockResponse(200, 'sales-invoice-list'),
        ]);

        $request = new GetPaginatedSalesInvoicesRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var SalesInvoiceCollection */
        $salesInvoices = $response->toResource();
        $this->assertInstanceOf(SalesInvoiceCollection::class, $salesInvoices);
        $this->assertGreaterThan(0, $salesInvoices->count());
    }

    /** @test */
    public function it_can_iterate_over_sales_invoices()
    {
        $client = new MockClient([
            GetPaginatedSalesInvoicesRequest::class => new MockResponse(200, 'sales-invoice-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'sales-invoice-list'),
                new MockResponse(200, 'empty-list', 'sales_invoices'),
            ),
        ]);

        $request = (new GetPaginatedSalesInvoicesRequest)->useIterator();

        /** @var Response */
        $response = $client->send($request);
        $this->assertTrue($response->successful());

        /** @var LazyCollection */
        $salesInvoices = $response->toResource();

        foreach ($salesInvoices as $salesInvoice) {
            $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedSalesInvoicesRequest;
        $this->assertEquals('sales-invoices', $request->resolveResourcePath());
    }
}
