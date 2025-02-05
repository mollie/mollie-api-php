<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\InvoiceLine;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\Recipient;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\PaymentTerm;
use Mollie\Api\Types\RecipientType;
use Mollie\Api\Types\SalesInvoiceStatus;
use Mollie\Api\Types\VatMode;
use Mollie\Api\Types\VatScheme;
use PHPUnit\Framework\TestCase;

class CreateSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_creates_sales_invoice()
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
        $request = new CreateSalesInvoiceRequest(
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

        /** @var SalesInvoice */
        $salesInvoice = $client->send($request);

        $this->assertTrue($salesInvoice->getResponse()->successful());
        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSalesInvoiceRequest(
            'EUR',
            SalesInvoiceStatus::DRAFT,
            VatScheme::STANDARD,
            VatMode::INCLUSIVE,
            PaymentTerm::DAYS_30,
            'XXXXX',
            new Recipient(RecipientType::CONSUMER, 'darth@vader.deathstar', 'Sample Street 12b', '2000 AA', 'Amsterdam', 'NL', 'nl_NL'),
            new DataCollection([new InvoiceLine('Monthly subscription fee', 1, '21', new Money('EUR', '10,00'))])
        );

        $this->assertEquals('sales-invoices', $request->resolveResourcePath());
    }
}
