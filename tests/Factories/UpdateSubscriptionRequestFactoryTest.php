<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdateSubscriptionRequestFactory;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use PHPUnit\Framework\TestCase;

class UpdateSubscriptionRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    private const SUBSCRIPTION_ID = 'sub_12345';

    /** @test */
    public function create_returns_update_subscription_request_object_with_full_data()
    {
        $request = UpdateSubscriptionRequestFactory::new(self::CUSTOMER_ID, self::SUBSCRIPTION_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
                'description' => 'Updated monthly subscription',
                'interval' => '1 month',
                'startDate' => '2024-01-01',
                'times' => 12,
                'metadata' => [
                    'subscription_id' => '12345',
                    'plan' => 'premium',
                ],
                'webhookUrl' => 'https://example.com/webhook',
                'mandateId' => 'mdt_12345',
            ])
            ->create();

        $this->assertInstanceOf(UpdateSubscriptionRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_subscription_request_object_with_minimal_data()
    {
        $request = UpdateSubscriptionRequestFactory::new(self::CUSTOMER_ID, self::SUBSCRIPTION_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
            ])
            ->create();

        $this->assertInstanceOf(UpdateSubscriptionRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_subscription_request_object_with_partial_data()
    {
        $request = UpdateSubscriptionRequestFactory::new(self::CUSTOMER_ID, self::SUBSCRIPTION_ID)
            ->withPayload([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '29.95',
                ],
                'description' => 'Updated monthly subscription',
                'interval' => '1 month',
                'times' => 12,
            ])
            ->create();

        $this->assertInstanceOf(UpdateSubscriptionRequest::class, $request);
    }
}
