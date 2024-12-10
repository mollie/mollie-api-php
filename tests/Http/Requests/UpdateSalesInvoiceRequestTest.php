<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\UpdateSalesInvoicePayload;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\SalesInvoiceStatus;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class UpdateSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_updates_sales_invoice()
    {
        $client = new MockClient([
            UpdateSalesInvoiceRequest::class => new MockResponse(200, 'sales-invoice'),
        ]);

        $payload = new UpdateSalesInvoicePayload(
            SalesInvoiceStatus::PAID,
            'XXXXX',
        );
        $request = new UpdateSalesInvoiceRequest('invoice_123', $payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(SalesInvoice::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateSalesInvoiceRequest('invoice_123', new UpdateSalesInvoicePayload(
            SalesInvoiceStatus::PAID,
            'XXXXX',
        ));
        $this->assertEquals('sales-invoices/invoice_123', $request->resolveResourcePath());
    }
}
