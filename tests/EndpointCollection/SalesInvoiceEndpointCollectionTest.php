<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\InvoiceLine;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\Recipient;
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
use PHPUnit\Framework\TestCase;

class SalesInvoiceEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetSalesInvoiceRequest::class => MockResponse::ok('sales-invoice'),
        ]);

        $salesInvoice = $client->salesInvoices->get('inv_123');

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateSalesInvoiceRequest::class => MockResponse::created('sales-invoice'),
        ]);

        $invoiceLines = [
            new InvoiceLine(
                'Monthly subscription fee',
                1,
                '21',
                new Money('EUR', '10,00'),
            ),
        ];

        // Create a sales invoice
        $payload = [
            'currency' => 'EUR',
            'status' => SalesInvoiceStatus::DRAFT,
            'vatScheme' => VatScheme::STANDARD,
            'vatMode' => VatMode::INCLUSIVE,
            'paymentTerm' => PaymentTerm::DAYS_30,
            'recipientIdentifier' => 'XXXXX',
            'recipient' => new Recipient(
                RecipientType::CONSUMER,
                'darth@vader.deathstar',
                'Sample Street 12b',
                '2000 AA',
                'Amsterdam',
                'NL',
                'nl_NL'
            ),
            'lines' => new DataCollection($invoiceLines),
        ];

        $salesInvoice = $client->salesInvoices->create($payload);

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdateSalesInvoiceRequest::class => MockResponse::ok('sales-invoice'),
        ]);

        $payload = [
            'status' => SalesInvoiceStatus::PAID,
            'recipientIdentifier' => 'XXXXX',
        ];
        $salesInvoice = $client->salesInvoices->update('invoice_123', $payload);

        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function delete()
    {
        $client = new MockMollieClient([
            DeleteSalesInvoiceRequest::class => MockResponse::noContent(),
        ]);

        $client->salesInvoices->delete('invoice_123');

        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok('sales-invoice-list'),
        ]);

        $salesInvoices = $client->salesInvoices->page();

        $this->assertInstanceOf(SalesInvoiceCollection::class, $salesInvoices);
    }

    /** @test */
    public function iterate()
    {
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok('sales-invoice-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'sales_invoices'),
        ]);

        /** @var SalesInvoice $salesInvoice */
        foreach ($client->salesInvoices->iterator() as $salesInvoice) {
            $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
        }
    }
}
