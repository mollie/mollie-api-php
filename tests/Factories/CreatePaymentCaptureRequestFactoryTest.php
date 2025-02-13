<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentCaptureRequestFactory;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use PHPUnit\Framework\TestCase;

class CreatePaymentCaptureRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_payment_capture_request_object_with_full_data()
    {
        $request = CreatePaymentCaptureRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Capture for order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'metadata' => [
                    'order_id' => '12345',
                    'description' => 'Manual capture for order',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentCaptureRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_capture_request_object_with_minimal_data()
    {
        $request = CreatePaymentCaptureRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Capture for order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentCaptureRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_capture_request_object_with_partial_data()
    {
        $request = CreatePaymentCaptureRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Capture for order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentCaptureRequest::class, $request);
    }
}
