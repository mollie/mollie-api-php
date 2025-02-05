<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedRefundsRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedRefundsRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_paginated_refunds_request_object_with_full_data()
    {
        $request = GetPaginatedRefundsRequestFactory::new()
            ->withQuery([
                'from' => 'ref_12345',
                'limit' => 50,
                'embed' => ['payment'],
                'profileId' => 'pfl_12345'
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_refunds_request_object_with_minimal_data()
    {
        $request = GetPaginatedRefundsRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_refunds_request_object_with_partial_data()
    {
        $request = GetPaginatedRefundsRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'profileId' => 'pfl_12345'
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
    }
}
