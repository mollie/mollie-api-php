<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use PHPUnit\Framework\TestCase;

class GetSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_fetches_sales_invoice()
    {
        $client = new MockMollieClient([
            GetSalesInvoiceRequest::class => MockResponse::ok('sales-invoice'),
        ]);

        $request = new GetSalesInvoiceRequest('invoice_123');

        /** @var SalesInvoice */
        $salesInvoice = $client->send($request);

        $this->assertTrue($salesInvoice->getResponse()->successful());
        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }
}
