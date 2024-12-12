<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreateSalesInvoicePayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\InvoiceLine;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\Recipient;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\PaymentTerm;
use Mollie\Api\Types\RecipientType;
use Mollie\Api\Types\SalesInvoiceStatus;
use Mollie\Api\Types\VatMode;
use Mollie\Api\Types\VatScheme;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateSalesInvoiceRequestTest extends TestCase
{
    /** @test */
    public function it_creates_sales_invoice()
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
            ),
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
        $request = new CreateSalesInvoiceRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(SalesInvoice::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSalesInvoiceRequest(new CreateSalesInvoicePayload(
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
            new DataCollection([
                new InvoiceLine(
                    'Monthly subscription fee',
                    1,
                    '21',
                    new Money('EUR', '10,00'),
                ),
            ])
        ));

        $this->assertEquals('sales-invoices', $request->resolveResourcePath());
    }
}
