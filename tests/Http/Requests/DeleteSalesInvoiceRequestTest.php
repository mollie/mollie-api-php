<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeleteSalesInvoiceRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DeleteSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_deletes_sales_invoice()
    {
        $client = new MockMollieClient([
            DeleteSalesInvoiceRequest::class => MockResponse::noContent(),
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
