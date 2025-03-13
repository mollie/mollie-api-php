<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Resources\Invoice;
use PHPUnit\Framework\TestCase;

class GetInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_invoice()
    {
        $client = new MockMollieClient([
            GetInvoiceRequest::class => MockResponse::ok('invoice'),
        ]);

        $invoiceId = 'inv_xBEbP9rvAq';
        $request = new GetInvoiceRequest($invoiceId);

        /** @var Invoice */
        $invoice = $client->send($request);

        $this->assertTrue($invoice->getResponse()->successful());
        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetInvoiceRequest('inv_xBEbP9rvAq');

        $this->assertEquals('invoices/inv_xBEbP9rvAq', $request->resolveResourcePath());
    }
}
