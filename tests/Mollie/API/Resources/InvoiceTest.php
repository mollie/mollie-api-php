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
            [InvoiceStatus::PAID, "isPaid", true],
            [InvoiceStatus::PAID, "isOpen", false],
            [InvoiceStatus::PAID, "isOverdue", false],

            [InvoiceStatus::OPEN, "isPaid", false],
            [InvoiceStatus::OPEN, "isOpen", true],
            [InvoiceStatus::OPEN, "isOverdue", false],

            [InvoiceStatus::OVERDUE, "isPaid", false],
            [InvoiceStatus::OVERDUE, "isOpen", false],
            [InvoiceStatus::OVERDUE, "isOverdue", true],
        ];
    }
}
