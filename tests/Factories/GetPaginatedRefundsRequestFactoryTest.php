<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedRefundsRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Types\PaymentIncludesQuery;
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
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('embed'));
    }

    /** @test */
    public function create_supports_legacy_embed_payment_query_key()
    {
        $request = GetPaginatedRefundsRequestFactory::new()
            ->withQuery([
                'embedPayment' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('embed'));
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
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedRefundsRequest::class, $request);
    }
}
