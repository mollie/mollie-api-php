<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentCaptureRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use PHPUnit\Framework\TestCase;

class GetPaymentCaptureRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';
    private const CAPTURE_ID = 'cap_12345';

    /** @test */
    public function create_returns_payment_capture_request_object_with_full_data()
    {
        $request = GetPaymentCaptureRequestFactory::new(self::PAYMENT_ID, self::CAPTURE_ID)
            ->withQuery([
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentCaptureRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_capture_request_object_with_minimal_data()
    {
        $request = GetPaymentCaptureRequestFactory::new(self::PAYMENT_ID, self::CAPTURE_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentCaptureRequest::class, $request);
    }
}
