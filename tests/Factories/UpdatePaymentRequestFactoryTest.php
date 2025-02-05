<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdatePaymentRequestFactory;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use PHPUnit\Framework\TestCase;

class UpdatePaymentRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_update_payment_request_object_with_full_data()
    {
        $request = UpdatePaymentRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Updated payment description',
                'redirectUrl' => 'https://example.com/redirect',
                'cancelUrl' => 'https://example.com/cancel',
                'webhookUrl' => 'https://example.com/webhook',
                'metadata' => [
                    'order_id' => '12345',
                    'customer_id' => '67890',
                ],
                'method' => 'ideal',
                'locale' => 'nl_NL',
                'restrictPaymentMethodsToCountry' => 'NL',
                'additional' => [
                    'customField' => 'customValue',
                ],
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_request_object_with_minimal_data()
    {
        $request = UpdatePaymentRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Updated payment description',
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_request_object_with_filtered_properties()
    {
        $request = UpdatePaymentRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Updated payment description',
                'metadata' => [
                    'order_id' => '12345',
                ],
                'invalidField' => 'This should be filtered out',
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentRequest::class, $request);
    }
}
