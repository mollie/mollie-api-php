<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSalesInvoiceRequest;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Types\SalesInvoiceStatus;
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

    /** @test */
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

        $this->assertSame(SalesInvoiceStatus::PAYMENT_REVERSED, $salesInvoice->status);
        $this->assertTrue($salesInvoice->isPaymentReversed());
        $this->assertFalse($salesInvoice->isPaid());
        $this->assertSame('failed', $salesInvoice->eInvoiceStatus);
    }

    /**
     * @test
     *
     * @dataProvider salesInvoiceStatusProvider
     */
    public function status_helpers_match_only_their_own_status(string $status, string $helper)
    {
        $client = new MockMollieClient([
            GetSalesInvoiceRequest::class => MockResponse::ok([
                'resource' => 'sales-invoice',
                'id' => 'invoice_123',
                'status' => $status,
            ]),
        ]);

        /** @var SalesInvoice */
        $salesInvoice = $client->send(new GetSalesInvoiceRequest('invoice_123'));

        foreach (self::salesInvoiceStatusProvider() as [$otherStatus, $otherHelper]) {
            $this->assertSame($otherStatus === $status, $salesInvoice->{$otherHelper}(), "{$otherHelper}() for status {$status}");
        }
    }

    public static function salesInvoiceStatusProvider(): array
    {
        return [
            [SalesInvoiceStatus::DRAFT, 'isDraft'],
            [SalesInvoiceStatus::ISSUING, 'isIssuing'],
            [SalesInvoiceStatus::ISSUED, 'isIssued'],
            [SalesInvoiceStatus::PENDING_PAYMENT, 'isPendingPayment'],
            [SalesInvoiceStatus::PAID, 'isPaid'],
            [SalesInvoiceStatus::OVERDUE, 'isOverdue'],
            [SalesInvoiceStatus::PAYMENT_REVERSED, 'isPaymentReversed'],
            [SalesInvoiceStatus::CANCELLED, 'isCancelled'],
            [SalesInvoiceStatus::EXPIRED, 'isExpired'],
            [SalesInvoiceStatus::FAILED, 'isFailed'],
        ];
    }
}
