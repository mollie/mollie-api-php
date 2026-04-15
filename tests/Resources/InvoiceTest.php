<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Types\InvoiceStatus;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestInvoiceStatuses
     */
    public function test_invoice_statuses($status, $function, $expected_boolean)
    {
        $invoice = new Invoice(
            $this->createMock(MollieApiClient::class),
        );
        $invoice->status = $status;

        $this->assertEquals($expected_boolean, $invoice->{$function}());
    }

    public function dpTestInvoiceStatuses()
    {
        return [
            [InvoiceStatus::Paid->value, 'isPaid', true],
            [InvoiceStatus::Paid->value, 'isOpen', false],
            [InvoiceStatus::Paid->value, 'isOverdue', false],

            [InvoiceStatus::Open->value, 'isPaid', false],
            [InvoiceStatus::Open->value, 'isOpen', true],
            [InvoiceStatus::Open->value, 'isOverdue', false],

            [InvoiceStatus::Overdue->value, 'isPaid', false],
            [InvoiceStatus::Overdue->value, 'isOpen', false],
            [InvoiceStatus::Overdue->value, 'isOverdue', true],
        ];
    }
}
