<?php

use Mollie\Api\Http\Data\PaymentDetails;
use PHPUnit\Framework\TestCase;

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
