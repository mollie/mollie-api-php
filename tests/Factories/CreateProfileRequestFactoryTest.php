<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateProfileRequestFactory;
use Mollie\Api\Http\Requests\CreateProfileRequest;
use PHPUnit\Framework\TestCase;

class CreateProfileRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_profile_request_object_with_full_data()
    {
        $request = CreateProfileRequestFactory::new()
            ->withPayload([
                'name' => 'My Webshop',
                'website' => 'https://www.mywebshop.com',
                'email' => 'info@mywebshop.com',
                'phone' => '+31612345678',
                'description' => 'Online store selling premium products',
                'countriesOfActivity' => ['NL', 'BE', 'DE'],
                'businessCategory' => 'RETAIL',
            ])
            ->create();

        $this->assertInstanceOf(CreateProfileRequest::class, $request);
    }

    /** @test */
    public function create_returns_profile_request_object_with_minimal_data()
    {
        $request = CreateProfileRequestFactory::new()
            ->withPayload([
                'name' => 'My Webshop',
                'website' => 'https://www.mywebshop.com',
                'email' => 'info@mywebshop.com',
                'phone' => '+31612345678',
            ])
            ->create();

        $this->assertInstanceOf(CreateProfileRequest::class, $request);
    }

    /** @test */
    public function create_returns_profile_request_object_with_partial_data()
    {
        $request = CreateProfileRequestFactory::new()
            ->withPayload([
                'name' => 'My Webshop',
                'website' => 'https://www.mywebshop.com',
                'email' => 'info@mywebshop.com',
                'phone' => '+31612345678',
                'description' => 'Online store selling premium products',
            ])
            ->create();

        $this->assertInstanceOf(CreateProfileRequest::class, $request);
    }
}
