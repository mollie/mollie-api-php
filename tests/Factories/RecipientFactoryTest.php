<?php

namespace Tests\Factories;

use Mollie\Api\Factories\RecipientFactory;
use Mollie\Api\Http\Data\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_recipient_object()
    {
        $recipient = RecipientFactory::new([
            'type' => 'individual',
            'email' => 'john@example.com',
            'streetAndNumber' => 'Main Street 1',
            'postalCode' => '1234AB',
            'city' => 'Amsterdam',
            'country' => 'NL',
            'locale' => 'nl_NL',
            'title' => 'Mr',
            'givenName' => 'John',
            'familyName' => 'Doe',
            'organizationName' => null,
            'organizationNumber' => null,
            'vatNumber' => null,
            'phone' => '+31612345678',
            'streetAdditional' => 'Floor 3',
            'region' => 'Noord-Holland',
        ])->create();

        $this->assertInstanceOf(Recipient::class, $recipient);
    }

    /** @test */
    public function create_returns_recipient_object_with_minimal_data()
    {
        $recipient = RecipientFactory::new([
            'type' => 'organization',
            'email' => 'org@example.com',
            'streetAndNumber' => 'Business Ave 42',
            'postalCode' => '5678CD',
            'city' => 'Rotterdam',
            'country' => 'NL',
            'locale' => 'nl_NL',
        ])->create();

        $this->assertInstanceOf(Recipient::class, $recipient);
    }
}
