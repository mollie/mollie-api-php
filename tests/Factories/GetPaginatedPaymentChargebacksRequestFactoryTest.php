<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedPaymentChargebacksRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentChargebacksRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_paginated_payment_chargebacks_request_object_with_full_data()
    {
        $request = GetPaginatedPaymentChargebacksRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'from' => 'chb_12345',
                'limit' => 50,
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_payment_chargebacks_request_object_with_minimal_data()
    {
        $request = GetPaginatedPaymentChargebacksRequestFactory::new(self::PAYMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_payment_chargebacks_request_object_with_partial_data()
    {
        $request = GetPaginatedPaymentChargebacksRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'limit' => 25,
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentChargebacksRequest::class, $request);
    }
}
