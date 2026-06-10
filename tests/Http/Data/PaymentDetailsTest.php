<?php

declare(strict_types=1);

use Mollie\Api\Http\Data\PaymentDetails;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaymentDetailsTest extends TestCase
{
    #[Test]
    public function from_array_creates_correct_object()
    {
        $object = PaymentDetails::fromArray($data = [
            'source' => 'banktransfer',
            'sourceReference' => 'Bank Transfer',
        ]);

        $this->assertInstanceOf(PaymentDetails::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
