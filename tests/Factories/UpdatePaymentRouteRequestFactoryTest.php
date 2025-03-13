<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdatePaymentRouteRequestFactory;
use Mollie\Api\Http\Requests\UpdatePaymentRouteRequest;
use PHPUnit\Framework\TestCase;

class UpdatePaymentRouteRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    private const ROUTE_ID = 'rt_12345';

    /** @test */
    public function create_returns_update_payment_route_request_object_with_valid_data()
    {
        $request = UpdatePaymentRouteRequestFactory::new(self::PAYMENT_ID, self::ROUTE_ID)
            ->withPayload([
                'releaseDate' => '2024-01-01',
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentRouteRequest::class, $request);
    }

    /** @test */
    public function create_throws_exception_when_release_date_is_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Release date is required');

        UpdatePaymentRouteRequestFactory::new(self::PAYMENT_ID, self::ROUTE_ID)
            ->create();
    }
}
