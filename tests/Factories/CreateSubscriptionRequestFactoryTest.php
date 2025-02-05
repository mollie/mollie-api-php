<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateSubscriptionRequestFactory;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use PHPUnit\Framework\TestCase;

class CreateSubscriptionRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    /** @test */
    public function create_returns_subscription_request_object_with_full_data()
    {
        $request = CreateSubscriptionRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
                'interval' => '1 month',
                'description' => 'Monthly subscription',
                'status' => 'active',
                'times' => 12,
                'startDate' => '2024-01-01',
                'method' => 'directdebit',
                'applicationFee' => [
                    'amount' => [
                        'currency' => 'EUR',
                        'value' => '1.00',
                    ],
                    'description' => 'Application fee',
                ],
                'metadata' => [
                    'subscription_id' => '12345',
                    'plan' => 'premium',
                ],
                'webhookUrl' => 'https://example.com/webhook',
                'mandateId' => 'mdt_12345',
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(CreateSubscriptionRequest::class, $request);
    }

    /** @test */
    public function create_returns_subscription_request_object_with_minimal_data()
    {
        $request = CreateSubscriptionRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
                'interval' => '1 month',
                'description' => 'Monthly subscription',
            ])
            ->create();

        $this->assertInstanceOf(CreateSubscriptionRequest::class, $request);
    }

    /** @test */
    public function create_returns_subscription_request_object_with_partial_data()
    {
        $request = CreateSubscriptionRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
                'interval' => '1 month',
                'description' => 'Monthly subscription',
                'status' => 'active',
                'times' => 12,
                'startDate' => '2024-01-01',
            ])
            ->create();

        $this->assertInstanceOf(CreateSubscriptionRequest::class, $request);
    }
}
