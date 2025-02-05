<?php

namespace Tests\Factories;

use Mollie\Api\Factories\InvoiceLineFactory;
use Mollie\Api\Http\Data\InvoiceLine;
use PHPUnit\Framework\TestCase;

class InvoiceLineFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_invoice_line_object_with_full_data()
    {
        $invoiceLine = InvoiceLineFactory::new([
            'description' => 'Test Invoice Line',
            'quantity' => 2,
            'vatRate' => '21.00',
            'unitPrice' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'discount' => [
                'type' => 'percentage',
                'value' => '10',
            ],
        ])->create();

        $this->assertInstanceOf(InvoiceLine::class, $invoiceLine);
    }

    /** @test */
    public function create_returns_invoice_line_object_with_minimal_data()
    {
        $invoiceLine = InvoiceLineFactory::new([
            'description' => 'Test Invoice Line',
            'quantity' => 1,
            'vatRate' => '21.00',
            'unitPrice' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
        ])->create();

        $this->assertInstanceOf(InvoiceLine::class, $invoiceLine);
    }
}
