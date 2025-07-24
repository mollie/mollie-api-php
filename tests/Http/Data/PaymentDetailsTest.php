<?php

use Mollie\Api\Http\Data\PaymentDetails;
use PHPUnit\Framework\TestCase;

class PaymentDetailsTest extends TestCase
{
    public function test_from_array_creates_correct_object()
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
