<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdateCustomerRequestFactory;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;
use PHPUnit\Framework\TestCase;

class UpdateCustomerRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    /** @test */
    public function create_returns_update_customer_request_object_with_full_data()
    {
        $request = UpdateCustomerRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'locale' => 'nl_NL',
                'metadata' => [
                    'order_id' => '12345',
                    'customer_type' => 'premium',
                ],
            ])
            ->create();

        $this->assertInstanceOf(UpdateCustomerRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_customer_request_object_with_minimal_data()
    {
        $request = UpdateCustomerRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'name' => 'John Doe',
            ])
            ->create();

        $this->assertInstanceOf(UpdateCustomerRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_customer_request_object_with_partial_data()
    {
        $request = UpdateCustomerRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'locale' => 'nl_NL',
            ])
            ->create();

        $this->assertInstanceOf(UpdateCustomerRequest::class, $request);
    }
}
