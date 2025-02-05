<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentLinkRequestFactory;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use PHPUnit\Framework\TestCase;

class CreatePaymentLinkRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_payment_link_request_object_with_full_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00'
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'webhookUrl' => 'https://example.com/webhook',
                'profileId' => 'pfl_12345',
                'reusable' => true,
                'expiresAt' => '2024-12-31',
                'allowedMethods' => ['ideal', 'creditcard']
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_link_request_object_with_minimal_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00'
                ],
                'redirectUrl' => 'https://example.com/redirect'
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_link_request_object_with_partial_data()
    {
        $request = CreatePaymentLinkRequestFactory::new()
            ->withPayload([
                'description' => 'Order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00'
                ],
                'redirectUrl' => 'https://example.com/redirect',
                'webhookUrl' => 'https://example.com/webhook',
                'profileId' => 'pfl_12345'
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentLinkRequest::class, $request);
    }
}
