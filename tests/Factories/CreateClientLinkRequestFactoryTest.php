<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateClientLinkRequestFactory;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use PHPUnit\Framework\TestCase;

class CreateClientLinkRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_client_link_request_object_with_full_data()
    {
        $request = CreateClientLinkRequestFactory::new()
            ->withPayload([
                'owner' => [
                    'givenName' => 'John',
                    'familyName' => 'Doe',
                    'email' => 'john@example.com',
                    'locale' => 'nl_NL',
                ],
                'name' => 'Example Company',
                'address' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
                'registrationNumber' => '12345678',
                'vatNumber' => 'NL123456789B01',
            ])
            ->create();

        $this->assertInstanceOf(CreateClientLinkRequest::class, $request);
    }

    /** @test */
    public function create_returns_client_link_request_object_with_minimal_data()
    {
        $request = CreateClientLinkRequestFactory::new()
            ->withPayload([
                'owner' => [
                    'givenName' => 'John',
                    'familyName' => 'Doe',
                    'email' => 'john@example.com',
                ],
                'name' => 'Example Company',
                'address' => [
                    'streetAndNumber' => 'Main Street 1',
                    'postalCode' => '1234AB',
                    'city' => 'Amsterdam',
                    'country' => 'NL',
                ],
            ])
            ->create();

        $this->assertInstanceOf(CreateClientLinkRequest::class, $request);
    }
}
