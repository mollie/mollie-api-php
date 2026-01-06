<?php

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Owner;
use PHPUnit\Framework\TestCase;

class OwnerTest extends TestCase
{
    /** @test */
    public function it_can_create_owner_from_array()
    {
        $object = Owner::fromArray($data = [
            'email' => 'john.doe@example.com',
            'givenName' => 'John',
            'familyName' => 'Doe',
            'locale' => 'nl_NL',
        ]);

        $this->assertInstanceOf(Owner::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
