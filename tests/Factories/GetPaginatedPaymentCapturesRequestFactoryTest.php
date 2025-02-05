<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedPaymentCapturesRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use PHPUnit\Framework\TestCase;

class GetPaginatedPaymentCapturesRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_paginated_payment_captures_request_object_with_full_data()
    {
        $request = GetPaginatedPaymentCapturesRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'from' => 'cap_12345',
                'limit' => 50,
                'include' => ['payment']
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentCapturesRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_payment_captures_request_object_with_minimal_data()
    {
        $request = GetPaginatedPaymentCapturesRequestFactory::new(self::PAYMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentCapturesRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_payment_captures_request_object_with_partial_data()
    {
        $request = GetPaginatedPaymentCapturesRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'limit' => 25
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedPaymentCapturesRequest::class, $request);
    }
}
