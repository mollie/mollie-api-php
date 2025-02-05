<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedSettlementsRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementsRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_paginated_settlements_request_object_with_full_data()
    {
        $request = GetPaginatedSettlementsRequestFactory::new()
            ->withQuery([
                'from' => 'stl_12345',
                'limit' => 50,
                'balanceId' => 'bal_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlements_request_object_with_minimal_data()
    {
        $request = GetPaginatedSettlementsRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlements_request_object_with_partial_data()
    {
        $request = GetPaginatedSettlementsRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'balanceId' => 'bal_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementsRequest::class, $request);
    }
}
