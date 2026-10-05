<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Config;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetInvoiceRequest;
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Resources\ResourceRegistry;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Resources\SalesInvoiceCollection;
use PHPUnit\Framework\TestCase;

class SalesInvoiceCollectionTest extends TestCase
{
    protected function tearDown(): void
    {
        Config::setResourceRegistryResolver(null);

        parent::tearDown();
    }

    public function testDefaultRegistryKeepsInvoiceTypesDistinct(): void
    {
        foreach ([new ResourceRegistry, ResourceRegistry::default()] as $registry) {
            $this->assertSame(Invoice::class, $registry->for('invoice'));
            $this->assertSame(Invoice::class, $registry->for('invoices'));
            $this->assertSame(SalesInvoice::class, $registry->for('sales-invoice'));
            $this->assertSame(SalesInvoice::class, $registry->for('sales-invoices'));
            $this->assertSame(SalesInvoice::class, $registry->for('sales_invoices'));
            $this->assertSame('invoice', $registry->singularOf(Invoice::class));
            $this->assertSame('invoices', $registry->pluralOf(Invoice::class));
            $this->assertSame('sales-invoice', $registry->singularOf(SalesInvoice::class));
            $this->assertSame('sales-invoices', $registry->pluralOf(SalesInvoice::class));
        }
    }

    public function testApiListHydratesSalesInvoices(): void
    {
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok('sales-invoice-list'),
        ]);

        $invoices = $client->send(new GetPaginatedSalesInvoicesRequest);

        $this->assertSame('invoices', SalesInvoiceCollection::$collectionName);
        $this->assertInstanceOf(SalesInvoiceCollection::class, $invoices);
        $this->assertCount(1, $invoices);
        $this->assertInstanceOf(SalesInvoice::class, $invoices->first());
        $this->assertSame('sales-invoice', $invoices->first()->resource);
        $this->assertSame('invoice_4Y0eZitmBnQ6IDoMqZQKh', $invoices->first()->id);
    }

    public function testApiPaginationHydratesNextAndPreviousPages(): void
    {
        $firstPage = MockResponse::ok('sales-invoice-list')->json();
        $secondPage = $firstPage;
        $secondPage['_embedded']['invoices'][0]['id'] = 'invoice_next';
        $secondPage['_links']['next'] = null;
        $secondPage['_links']['previous'] = [
            'href' => 'https://api.mollie.com/v2/sales-invoices?from=invoice_first',
            'type' => 'application/hal+json',
        ];
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok($firstPage),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok($secondPage),
                MockResponse::ok($firstPage),
            ),
        ]);

        $invoices = $client->send(new GetPaginatedSalesInvoicesRequest);
        $next = $invoices->next();

        $this->assertInstanceOf(SalesInvoiceCollection::class, $next);
        $this->assertInstanceOf(SalesInvoice::class, $next->first());
        $this->assertSame('invoice_next', $next->first()->id);
        $this->assertNull($next->next());

        $previous = $next->previous();

        $this->assertInstanceOf(SalesInvoiceCollection::class, $previous);
        $this->assertInstanceOf(SalesInvoice::class, $previous->first());
        $this->assertSame($invoices->first()->id, $previous->first()->id);
        $client->assertSentCount(3);
    }

    public function testLegacySalesInvoiceKeysRemainSupported(): void
    {
        foreach (['sales_invoices', 'sales-invoices'] as $key) {
            $data = MockResponse::ok('sales-invoice-list')->json();
            $data['_embedded'][$key] = $data['_embedded']['invoices'];
            unset($data['_embedded']['invoices']);
            $client = new MockMollieClient([
                GetPaginatedSalesInvoicesRequest::class => MockResponse::ok($data),
            ]);

            $invoices = $client->send(new GetPaginatedSalesInvoicesRequest);

            $this->assertCount(1, $invoices);
            $this->assertInstanceOf(SalesInvoice::class, $invoices->first());
        }
    }

    public function testRegistryKeyTakesPrecedenceEvenForAnEmptyPage(): void
    {
        $data = MockResponse::ok('sales-invoice-list')->json();
        $data['_embedded']['sales_invoices'] = [];
        $data['count'] = 0;
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok($data),
        ]);

        $this->assertCount(0, $client->send(new GetPaginatedSalesInvoicesRequest));
    }

    public function testCustomRegistryCollectionKeysRemainSupported(): void
    {
        $registry = new ResourceRegistry;
        $registry->register(SalesInvoice::class, 'custom-sales-invoices');
        Config::setResourceRegistryResolver(fn () => $registry);
        $this->assertSame('custom-sales-invoices', SalesInvoiceCollection::getCollectionResourceName());
        $data = MockResponse::ok('sales-invoice-list')->json();
        $data['_embedded']['custom_sales_invoices'] = $data['_embedded']['invoices'];
        unset($data['_embedded']['invoices']);
        $client = new MockMollieClient([
            GetPaginatedSalesInvoicesRequest::class => MockResponse::ok($data),
        ]);

        $invoices = $client->send(new GetPaginatedSalesInvoicesRequest);

        $this->assertCount(1, $invoices);
        $this->assertInstanceOf(SalesInvoice::class, $invoices->first());
    }

    public function testInvoiceCollectionsAndIndividualResourcesRemainDistinct(): void
    {
        $client = new MockMollieClient([
            GetPaginatedInvoiceRequest::class => MockResponse::ok('invoice-list'),
            GetInvoiceRequest::class => MockResponse::ok('invoice', 'inv_regular'),
            GetSalesInvoiceRequest::class => MockResponse::ok('sales-invoice'),
        ]);

        $invoices = $client->send(new GetPaginatedInvoiceRequest);
        $invoice = $client->send(new GetInvoiceRequest('inv_regular'));
        $salesInvoice = $client->send(new GetSalesInvoiceRequest('invoice_sales'));

        $this->assertInstanceOf(InvoiceCollection::class, $invoices);
        $this->assertInstanceOf(Invoice::class, $invoices->first());
        $this->assertSame('invoice', $invoices->first()->resource);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame('invoice', $invoice->resource);
        $this->assertInstanceOf(SalesInvoice::class, $salesInvoice);
        $this->assertSame('sales-invoice', $salesInvoice->resource);
    }
}
