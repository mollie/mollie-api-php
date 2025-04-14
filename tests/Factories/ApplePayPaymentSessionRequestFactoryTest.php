<?php

namespace Tests\Factories;

use Mollie\Api\Factories\ApplePayPaymentSessionRequestFactory;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use PHPUnit\Framework\TestCase;

class ApplePayPaymentSessionRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_apple_pay_payment_session_request_object()
    {
        $factory = ApplePayPaymentSessionRequestFactory::new()
            ->withPayload([
                'domain' => 'example.com',
                'validationUrl' => 'https://example.com/validation',
                'profileId' => '1234567890',
            ])
            ->create();

        $this->assertInstanceOf(ApplePayPaymentSessionRequest::class, $factory);
    }
}
