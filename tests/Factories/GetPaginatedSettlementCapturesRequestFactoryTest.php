<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedSettlementCapturesRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedSettlementCapturesRequestFactoryTest extends TestCase
{
    private const SETTLEMENT_ID = 'stl_12345';

    /** @test */
    public function create_returns_paginated_settlement_captures_request_object_with_full_data()
    {
        $request = GetPaginatedSettlementCapturesRequestFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'from' => 'cap_12345',
                'limit' => 50,
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementCapturesRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_captures_request_object_with_minimal_data()
    {
        $request = GetPaginatedSettlementCapturesRequestFactory::new(self::SETTLEMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementCapturesRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_settlement_captures_request_object_with_partial_data()
    {
        $request = GetPaginatedSettlementCapturesRequestFactory::new(self::SETTLEMENT_ID)
            ->withQuery([
                'limit' => 25,
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedSettlementCapturesRequest::class, $request);
    }
}
