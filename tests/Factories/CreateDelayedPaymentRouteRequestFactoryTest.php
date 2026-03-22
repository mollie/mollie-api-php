<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateDelayedPaymentRouteRequestFactory;
use Mollie\Api\Http\Requests\CreateDelayedPaymentRouteRequest;
use PHPUnit\Framework\TestCase;

class CreateDelayedPaymentRouteRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_create_delayed_route_request_object_with_full_data()
    {
        $request = CreateDelayedPaymentRouteRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'amount' => ['value' => '10.00', 'currency' => 'EUR'],
                'destination' => ['type' => 'organization', 'organizationId' => 'org_12345'],
            ])
            ->create();

        $this->assertInstanceOf(CreateDelayedPaymentRouteRequest::class, $request);
    }

    /** @test */
    public function create_throws_exception_when_amount_is_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Amount is required');

        CreateDelayedPaymentRouteRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'destination' => ['type' => 'organization', 'organizationId' => 'org_12345'],
            ])
            ->create();
    }

    /** @test */
    public function create_throws_exception_when_destination_is_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Destination is required');

        CreateDelayedPaymentRouteRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'amount' => ['value' => '10.00', 'currency' => 'EUR'],
            ])
            ->create();
    }
}
