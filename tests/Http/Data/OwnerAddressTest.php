<?php

use Mollie\Api\Http\Data\OwnerAddress;
use PHPUnit\Framework\TestCase;

class OwnerAddressTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_array()
    {
        $object = OwnerAddress::fromArray($data = [
            'country' => 'NL',
            'streetAndNumber' => 'Dam 1',
            'postalCode' => '1012JS',
            'region' => 'Noord-Holland',
            'city' => null,
        ]);

        $this->assertInstanceOf(OwnerAddress::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
