<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DeleteSalesInvoiceRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class DeleteSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_deletes_sales_invoice()
    {
        $client = new MockClient([
            DeleteSalesInvoiceRequest::class => new MockResponse(204),
        ]);

        $request = new DeleteSalesInvoiceRequest('invoice_123');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new DeleteSalesInvoiceRequest('invoice_123');
        $this->assertEquals('sales-invoices/invoice_123', $request->resolveResourcePath());
    }
}
