<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_fetches_sales_invoice()
    {
        $client = new MockClient([
            GetSalesInvoiceRequest::class => new MockResponse(200, 'sales-invoice'),
        ]);

        $request = new GetSalesInvoiceRequest('invoice_123');

        /** @var SalesInvoice */
        $salesInvoice = $client->send($request);

        $this->assertTrue($salesInvoice->getResponse()->successful());
        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }
}
