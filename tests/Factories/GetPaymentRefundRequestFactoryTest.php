<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentRefundRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use PHPUnit\Framework\TestCase;

class GetPaymentRefundRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    private const REFUND_ID = 'ref_12345';

    /** @test */
    public function create_returns_payment_refund_request_object_with_full_data()
    {
        $request = GetPaymentRefundRequestFactory::new(self::PAYMENT_ID, self::REFUND_ID)
            ->withQuery([
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentRefundRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_refund_request_object_with_minimal_data()
    {
        $request = GetPaymentRefundRequestFactory::new(self::PAYMENT_ID, self::REFUND_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentRefundRequest::class, $request);
    }
}
