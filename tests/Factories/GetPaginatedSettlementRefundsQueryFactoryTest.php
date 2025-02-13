<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedSettlementRefundsQueryFactory;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementRefundsQueryFactoryTest extends TestCase
{
    private const SETTLEMENT_ID = 'stl_12345';

    /** @test */
    public function create_returns_paginated_settlement_refunds_request_object_with_full_data()
    {
        $request = GetPaginatedSettlementRefundsQueryFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'from' => 'ref_12345',
                'limit' => 50,
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementRefundsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_refunds_request_object_with_minimal_data()
    {
        $request = GetPaginatedSettlementRefundsQueryFactory::new(self::SETTLEMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementRefundsRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_refunds_request_object_with_partial_data()
    {
        $request = GetPaginatedSettlementRefundsQueryFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'limit' => 25,
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementRefundsRequest::class, $request);
    }
}
