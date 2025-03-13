<?php

namespace Tests\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Factories\RefundRouteCollectionFactory;
use Mollie\Api\Http\Data\DataCollection;
use PHPUnit\Framework\TestCase;

class RefundRouteCollectionFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_refund_route_collection()
    {
        $collection = RefundRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'source' => [
                    'organizationId' => 'org_123456',
                ],
            ],
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '20.00',
                ],
                'source' => [
                    'organizationId' => 'org_789012',
                ],
            ],
        ])->create();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    /** @test */
    public function create_returns_refund_route_collection_with_single_route()
    {
        $collection = RefundRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'source' => [
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
        $this->expectExceptionMessage('Invalid RefundRoute data provided');

        RefundRouteCollectionFactory::new([
            [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                // missing source.organizationId
            ],
        ])->create();
    }
}
