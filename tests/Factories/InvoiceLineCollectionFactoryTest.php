<?php

namespace Tests\Factories;

use Mollie\Api\Factories\InvoiceLineCollectionFactory;
use Mollie\Api\Http\Data\DataCollection;
use PHPUnit\Framework\TestCase;

class InvoiceLineCollectionFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_invoice_line_collection()
    {
        $collection = InvoiceLineCollectionFactory::new([
            [
                'description' => 'First Invoice Line',
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
            ],
            [
                'description' => 'Second Invoice Line',
                'quantity' => 1,
                'vatRate' => '21.00',
                'unitPrice' => [
                    'currency' => 'EUR',
                    'value' => '15.00',
                ],
            ],
        ])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
    }

    /** @test */
    public function create_returns_empty_collection_for_empty_input()
    {
        $collection = InvoiceLineCollectionFactory::new([])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(0, $collection);
    }
}
