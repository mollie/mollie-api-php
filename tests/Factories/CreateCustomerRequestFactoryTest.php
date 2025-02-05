<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateCustomerRequestFactory;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use PHPUnit\Framework\TestCase;

class CreateCustomerRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_customer_request_object_with_full_data()
    {
        $request = CreateCustomerRequestFactory::new()
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

        $this->assertInstanceOf(CreateCustomerRequest::class, $request);
    }

    /** @test */
    public function create_returns_customer_request_object_with_minimal_data()
    {
        $request = CreateCustomerRequestFactory::new()
            ->withPayload([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ])
            ->create();

        $this->assertInstanceOf(CreateCustomerRequest::class, $request);
    }

    /** @test */
    public function create_returns_customer_request_object_with_partial_data()
    {
        $request = CreateCustomerRequestFactory::new()
            ->withPayload([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'locale' => 'nl_NL',
            ])
            ->create();

        $this->assertInstanceOf(CreateCustomerRequest::class, $request);
    }
}
