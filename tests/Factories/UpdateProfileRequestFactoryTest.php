<?php

namespace Tests\Factories;

use Mollie\Api\Factories\UpdateProfileRequestFactory;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use PHPUnit\Framework\TestCase;

class UpdateProfileRequestFactoryTest extends TestCase
{
    private const PROFILE_ID = 'pfl_12345';

    /** @test */
    public function create_returns_update_profile_request_object_with_full_data()
    {
        $request = UpdateProfileRequestFactory::new(self::PROFILE_ID)
            ->withPayload([
                'name' => 'My Updated Webshop',
                'website' => 'https://www.mywebshop.com',
                'email' => 'info@mywebshop.com',
                'phone' => '+31612345678',
                'description' => 'Online store selling premium products',
                'countriesOfActivity' => ['NL', 'BE', 'DE'],
                'businessCategory' => 'RETAIL',
                'mode' => 'live',
            ])
            ->create();

        $this->assertInstanceOf(UpdateProfileRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_profile_request_object_with_minimal_data()
    {
        $request = UpdateProfileRequestFactory::new(self::PROFILE_ID)
            ->withPayload([
                'name' => 'My Updated Webshop',
            ])
            ->create();

        $this->assertInstanceOf(UpdateProfileRequest::class, $request);
    }

    /** @test */
    public function create_returns_update_profile_request_object_with_partial_data()
    {
        $request = UpdateProfileRequestFactory::new(self::PROFILE_ID)
            ->withPayload([
                'name' => 'My Updated Webshop',
                'website' => 'https://www.mywebshop.com',
                'email' => 'info@mywebshop.com',
                'phone' => '+31612345678',
                'description' => 'Online store selling premium products',
            ])
            ->create();

        $this->assertInstanceOf(UpdateProfileRequest::class, $request);
    }
}
