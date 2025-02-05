<?php

namespace Tests\Factories;

use Mollie\Api\Factories\OrderLineFactory;
use Mollie\Api\Http\Data\OrderLine;
use PHPUnit\Framework\TestCase;

class OrderLineFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_order_line_object_with_full_data()
    {
        $orderLine = OrderLineFactory::new([
            'description' => 'Test Product',
            'quantity' => 2,
            'unitPrice' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'totalAmount' => [
                'currency' => 'EUR',
                'value' => '20.00',
            ],
            'type' => 'physical',
            'quantityUnit' => 'pcs',
            'discountAmount' => [
                'currency' => 'EUR',
                'value' => '5.00',
            ],
            'recurring' => [
                'times' => 12,
                'interval' => '1 month',
            ],
            'vatRate' => '21.00',
            'vatAmount' => [
                'currency' => 'EUR',
                'value' => '4.20',
            ],
            'sku' => 'PROD-123',
            'imageUrl' => 'https://example.com/image.jpg',
            'productUrl' => 'https://example.com/product',
        ])->create();

        $this->assertInstanceOf(OrderLine::class, $orderLine);
    }

    /** @test */
    public function create_returns_order_line_object_with_minimal_data()
    {
        $orderLine = OrderLineFactory::new([
            'description' => 'Test Product',
            'quantity' => 1,
            'unitPrice' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'totalAmount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'type' => 'digital',
            'vatRate' => '21.00',
        ])->create();

        $this->assertInstanceOf(OrderLine::class, $orderLine);
    }
}
