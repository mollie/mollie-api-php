<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use PHPUnit\Framework\TestCase;

class InvoiceEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetInvoiceRequest::class => MockResponse::ok('invoice'),
        ]);

        /** @var Invoice $invoice */
        $invoice = $client->invoices->get('inv_xBEbP9rvAq');

        $this->assertInvoice($invoice);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedInvoiceRequest::class => MockResponse::ok('invoice-list'),
        ]);

        /** @var InvoiceCollection $invoices */
        $invoices = $client->invoices->page();

        $this->assertInstanceOf(InvoiceCollection::class, $invoices);
        $this->assertEquals(1, $invoices->count());
        $this->assertCount(1, $invoices);

        $this->assertInvoice($invoices[0]);
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedInvoiceRequest::class => MockResponse::ok('invoice-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'invoices'),
        ]);

        foreach ($client->invoices->iterator() as $invoice) {
            $this->assertInstanceOf(Invoice::class, $invoice);
            $this->assertInvoice($invoice);
        }
    }

    protected function assertInvoice(Invoice $invoice)
    {
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('invoice', $invoice->resource);
        $this->assertEquals('2023.10000', $invoice->reference);
        $this->assertEquals('NL001234567B01', $invoice->vatNumber);
        $this->assertEquals('open', $invoice->status);
        $this->assertEquals('45.00', $invoice->netAmount->value);
        $this->assertEquals('EUR', $invoice->netAmount->currency);
        $this->assertEquals('9.45', $invoice->vatAmount->value);
        $this->assertEquals('54.45', $invoice->grossAmount->value);
        $this->assertNotEmpty($invoice->lines);
        $this->assertEquals('2023-09-01', $invoice->issuedAt);
        $this->assertEquals('2023-09-14', $invoice->dueAt);
    }
}
