<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Payload\CreateSalesInvoicePayload;
use Mollie\Api\Http\Payload\DataCollection;
use Mollie\Api\Http\Payload\InvoiceLine;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\Recipient;
use Mollie\Api\Http\Payload\UpdateSalesInvoicePayload;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;
use Mollie\Api\Http\Requests\DeleteSalesInvoiceRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Resources\SalesInvoiceCollection;
use Mollie\Api\Types\PaymentTerm;
use Mollie\Api\Types\RecipientType;
use Mollie\Api\Types\SalesInvoiceStatus;
use Mollie\Api\Types\VatMode;
use Mollie\Api\Types\VatScheme;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class SalesInvoiceEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetSalesInvoiceRequest::class => new MockResponse(200, 'sales-invoice'),
        ]);

        $salesInvoice = $client->salesInvoices->get('inv_123');

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreateSalesInvoiceRequest::class => new MockResponse(201, 'sales-invoice'),
        ]);

        $invoiceLines = [
            new InvoiceLine(
                'Monthly subscription fee',
                1,
                '21',
                new Money('EUR', '10,00'),
            )
        ];

        // Create a sales invoice
        $payload = new CreateSalesInvoicePayload(
            'EUR',
            SalesInvoiceStatus::DRAFT,
            VatScheme::STANDARD,
            VatMode::INCLUSIVE,
            PaymentTerm::DAYS_30,
            'XXXXX',
            new Recipient(
                RecipientType::CONSUMER,
                'darth@vader.deathstar',
                'Sample Street 12b',
                '2000 AA',
                'Amsterdam',
                'NL',
                'nl_NL'
            ),
            new DataCollection($invoiceLines)
        );

        $salesInvoice = $client->salesInvoices->create($payload);

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function update()
    {
        $client = new MockClient([
            UpdateSalesInvoiceRequest::class => new MockResponse(200, 'sales-invoice'),
        ]);

        $payload = new UpdateSalesInvoicePayload(
            SalesInvoiceStatus::PAID,
            'XXXXX',
        );
        $salesInvoice = $client->salesInvoices->update('invoice_123', $payload);

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function delete()
    {
        $client = new MockClient([
            DeleteSalesInvoiceRequest::class => new MockResponse(204),
        ]);

        $client->salesInvoices->delete('invoice_123');

        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedSalesInvoicesRequest::class => new MockResponse(200, 'sales-invoice-list'),
        ]);

        $salesInvoices = $client->salesInvoices->page();

        $this->assertInstanceOf(SalesInvoiceCollection::class, $salesInvoices);
    }

    /** @test */
    public function iterate()
    {
        $client = new MockClient([
            GetPaginatedSalesInvoicesRequest::class => new MockResponse(200, 'sales-invoice-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'sales_invoices'),
        ]);

        /** @var SalesInvoice $salesInvoice */
        foreach ($client->salesInvoices->iterator() as $salesInvoice) {
            $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
        }
    }
}
