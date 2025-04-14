<?php

namespace Tests\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Factories\PaymentRouteCollectionFactory;
use Mollie\Api\Http\Data\DataCollection;
use PHPUnit\Framework\TestCase;

class PaymentRouteCollectionFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_payment_route_collection_with_full_data()
    {
        $collection = PaymentRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'destination' => [
                    'organizationId' => 'org_123456',
                ],
                'delayUntil' => '2024-12-31',
            ],
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '20.00',
                ],
                'destination' => [
                    'organizationId' => 'org_789012',
                ],
                'delayUntil' => '2025-01-15',
            ],
        ])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    /** @test */
    public function create_returns_payment_route_collection_with_minimal_data()
    {
        $collection = PaymentRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'destination' => [
                    'organizationId' => 'org_123456',
                ],
            ],
        ])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(1, $collection);
    }

    /** @test */
    public function create_throws_exception_for_invalid_data()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid PaymentRoute data provided');

        PaymentRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                // missing destination.organizationId
            ],
        ])->create();
    }
}
