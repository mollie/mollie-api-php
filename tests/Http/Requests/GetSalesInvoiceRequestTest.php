<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\SalesInvoiceEInvoiceStatus;
use Mollie\Api\Types\SalesInvoiceStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetSalesInvoiceRequestTest extends TestCase
{
    #[Test]
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

    #[Test]
    public function it_hydrates_response_only_statuses_and_the_e_invoice_status()
    {
        $client = new MockMollieClient([
            GetSalesInvoiceRequest::class => MockResponse::ok([
                'resource' => 'sales-invoice',
                'id' => 'invoice_123',
                'status' => 'payment-reversed',
                'isEInvoice' => true,
                'eInvoiceStatus' => 'failed',
            ]),
        ]);

        /** @var SalesInvoice */
        $salesInvoice = $client->send(new GetSalesInvoiceRequest('invoice_123'));

        $this->assertSame(SalesInvoiceStatus::PaymentReversed, $salesInvoice->status);
        $this->assertTrue($salesInvoice->isPaymentReversed());
        $this->assertFalse($salesInvoice->isPaid());
        $this->assertSame(SalesInvoiceEInvoiceStatus::Failed, $salesInvoice->eInvoiceStatus);
    }
}
