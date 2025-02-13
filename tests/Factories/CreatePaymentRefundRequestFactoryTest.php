<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreatePaymentRefundRequestFactory;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use PHPUnit\Framework\TestCase;

class CreatePaymentRefundRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_payment_refund_request_object_with_full_data()
    {
        $request = CreatePaymentRefundRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Order refund',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'metadata' => [
                    'order_id' => '12345',
                    'reason' => 'customer_request',
                ],
                'reverseRouting' => true,
                'routingReversals' => [
                    [
                        'amount' => [
                            'currency' => 'EUR',
                            'value' => '50.00',
                        ],
                        'source' => [
                            'organizationId' => 'org_12345',
                        ],
                    ],
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentRefundRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_refund_request_object_with_minimal_data()
    {
        $request = CreatePaymentRefundRequestFactory::new(self::PAYMENT_ID)
            ->withPayload([
                'description' => 'Refund for order #12345',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'metadata' => [
                    'order_id' => '12345',
                    'reason' => 'customer_request',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreatePaymentRefundRequest::class, $request);
    }
}
