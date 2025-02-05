<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedSettlementChargebacksRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedSettlementChargebacksRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementChargebacksRequestFactoryTest extends TestCase
{
    private const SETTLEMENT_ID = 'stl_12345';

    /** @test */
    public function create_returns_paginated_settlement_chargebacks_request_object_with_full_data()
    {
        $request = GetPaginatedSettlementChargebacksRequestFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'from' => 'chb_12345',
                'limit' => 50,
                'include' => ['payment'],
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_chargebacks_request_object_with_minimal_data()
    {
        $request = GetPaginatedSettlementChargebacksRequestFactory::new(self::SETTLEMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementChargebacksRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_chargebacks_request_object_with_partial_data()
    {
        $request = GetPaginatedSettlementChargebacksRequestFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'limit' => 25,
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementChargebacksRequest::class, $request);
    }
}
