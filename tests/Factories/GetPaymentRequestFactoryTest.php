<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use PHPUnit\Framework\TestCase;

class GetPaymentRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_payment_request_object_with_full_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'embed' => ['captures', 'refunds', 'chargebacks'],
                'include' => ['qrCode', 'remainderDetails']
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_request_object_with_minimal_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_request_object_with_partial_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'embed' => ['captures'],
                'include' => ['qrCode']
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
    }
}
