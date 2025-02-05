<?php

namespace Tests\Factories;

use Mollie\Api\Factories\OrderLineCollectionFactory;
use Mollie\Api\Http\Data\DataCollection;
use PHPUnit\Framework\TestCase;

class OrderLineCollectionFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_order_line_collection()
    {
        $collection = OrderLineCollectionFactory::new([
            [
                'description' => 'First Product',
                'quantity' => 2,
                'vatRate' => '21.00',
                'unitPrice' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'totalAmount' => [
                    'currency' => 'EUR',
                    'value' => '20.00',
                ],
                'type' => 'physical',
            ],
            [
                'description' => 'Second Product',
                'quantity' => 1,
                'vatRate' => '21.00',
                'unitPrice' => [
                    'currency' => 'EUR',
                    'value' => '15.00',
                ],
                'totalAmount' => [
                    'currency' => 'EUR',
                    'value' => '15.00',
                ],
                'type' => 'digital',
            ],
        ])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    /** @test */
    public function create_returns_empty_collection_for_empty_input()
    {
        $collection = OrderLineCollectionFactory::new([])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(0, $collection);
    }
}
