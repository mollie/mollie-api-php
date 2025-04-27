<?php

use PHPUnit\Framework\TestCase;
use Mollie\Api\Http\Data\PaymentDetails;

class PaymentDetailsTest extends TestCase
{
    public function testFromArrayCreatesCorrectObject()
    {
        $object = PaymentDetails::fromArray($data = [
            'source' => 'banktransfer',
            'sourceDescription' => 'Bank Transfer',
        ]);

        $this->assertInstanceOf(PaymentDetails::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
