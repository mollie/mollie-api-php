<?php

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /** @test */
    public function it_can_create_address_from_array()
    {
        $object = Address::fromArray($data = [
            'title' => 'Mr.',
            'familyName' => 'Doe',
            'organizationName' => 'Mollie',
            'givenName' => 'John',
            'streetAndNumber' => 'Dam 1',
            'streetAdditional' => 'Apt 2',
            'postalCode' => '1012JS',
            'email' => 'john.doe@example.com',
            'phone' => '+31201234567',
            'city' => 'Amsterdam',
            'country' => 'NL',
            'region' => 'Noord-Holland',
        ]);

        $this->assertInstanceOf(Address::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
