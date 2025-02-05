<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedChargebacksRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedChargebacksRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_paginated_chargebacks_request_object_with_full_data()
    {
        $request = GetPaginatedChargebacksRequestFactory::new()
            ->withQuery([
                'from' => 'chb_12345',
                'limit' => 50,
                'include' => ['payment'],
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_chargebacks_request_object_with_minimal_data()
    {
        $request = GetPaginatedChargebacksRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetPaginatedChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_chargebacks_request_object_with_partial_data()
    {
        $request = GetPaginatedChargebacksRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedChargebacksRequest::class, $request);
    }
}
