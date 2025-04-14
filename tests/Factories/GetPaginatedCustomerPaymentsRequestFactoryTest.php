<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedCustomerPaymentsRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedCustomerPaymentsRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    /** @test */
    public function create_returns_paginated_customer_payments_request_object_with_full_data()
    {
        $request = GetPaginatedCustomerPaymentsRequestFactory::new(self::CUSTOMER_ID)
            ->withQuery([
                'from' => 'tr_12345',
                'limit' => 50,
                'sort' => 'created_at',
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedCustomerPaymentsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_customer_payments_request_object_with_minimal_data()
    {
        $request = GetPaginatedCustomerPaymentsRequestFactory::new(self::CUSTOMER_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedCustomerPaymentsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_customer_payments_request_object_with_partial_data()
    {
        $request = GetPaginatedCustomerPaymentsRequestFactory::new(self::CUSTOMER_ID)
            ->withQuery([
                'limit' => 25,
                'sort' => 'created_at',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedCustomerPaymentsRequest::class, $request);
    }
}
