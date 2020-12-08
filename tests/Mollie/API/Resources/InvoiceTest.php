<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Types\InvoiceStatus;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestInvoiceStatuses
     */
    public function testInvoiceStatuses($status, $function, $expected_boolean)
    {
        $invoice = new Invoice($this->createMock(MollieApiClient::class));
        $invoice->status = $status;

        $this->assertEquals($expected_boolean, $invoice->{$function}());
    }

    public function dpTestInvoiceStatuses()
    {
        return [
            [InvoiceStatus::STATUS_PAID, "isPaid", true],
            [InvoiceStatus::STATUS_PAID, "isOpen", false],
            [InvoiceStatus::STATUS_PAID, "isOverdue", false],

            [InvoiceStatus::STATUS_OPEN, "isPaid", false],
            [InvoiceStatus::STATUS_OPEN, "isOpen", true],
            [InvoiceStatus::STATUS_OPEN, "isOverdue", false],

            [InvoiceStatus::STATUS_OVERDUE, "isPaid", false],
            [InvoiceStatus::STATUS_OVERDUE, "isOpen", false],
            [InvoiceStatus::STATUS_OVERDUE, "isOverdue", true],
        ];
    }
}
