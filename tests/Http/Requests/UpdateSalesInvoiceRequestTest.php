<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\SalesInvoiceStatus;
use PHPUnit\Framework\TestCase;

class UpdateSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_updates_sales_invoice()
    {
        $client = new MockMollieClient([
            UpdateSalesInvoiceRequest::class => MockResponse::ok('sales-invoice'),
        ]);

        $request = new UpdateSalesInvoiceRequest('invoice_123', SalesInvoiceStatus::PAID, 'XXXXX');

        /** @var SalesInvoice */
        $salesInvoice = $client->send($request);

        $this->assertTrue($salesInvoice->getResponse()->successful());
        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdateSalesInvoiceRequest('invoice_123', SalesInvoiceStatus::PAID, 'XXXXX');

        $this->assertEquals('sales-invoices/invoice_123', $request->resolveResourcePath());
    }
}
