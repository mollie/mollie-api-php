<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentChargebackRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use PHPUnit\Framework\TestCase;

class GetPaymentChargebackRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';
    private const CHARGEBACK_ID = 'chb_12345';

    /** @test */
    public function create_returns_payment_chargeback_request_object_with_full_data()
    {
        $request = GetPaymentChargebackRequestFactory::new(self::PAYMENT_ID, self::CHARGEBACK_ID)
            ->withQuery([
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentChargebackRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_chargeback_request_object_with_minimal_data()
    {
        $request = GetPaymentChargebackRequestFactory::new(self::PAYMENT_ID, self::CHARGEBACK_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentChargebackRequest::class, $request);
    }
}
