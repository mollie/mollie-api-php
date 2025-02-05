<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdatePaymentLinkRequestFactory;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use PHPUnit\Framework\TestCase;

class UpdatePaymentLinkRequestFactoryTest extends TestCase
{
    private const PAYMENT_LINK_ID = 'pl_12345';

    /** @test */
    public function create_returns_update_payment_link_request_object_with_full_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
                'archived' => true,
                'allowedMethods' => ['ideal', 'creditcard'],
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_link_request_object_with_minimal_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_payment_link_request_object_with_partial_data()
    {
        $request = UpdatePaymentLinkRequestFactory::new(self::PAYMENT_LINK_ID)
            ->withPayload([
                'description' => 'Updated payment link description',
                'archived' => true,
            ])
            ->create();

        $this->assertInstanceOf(UpdatePaymentLinkRequest::class, $request);
    }
}
