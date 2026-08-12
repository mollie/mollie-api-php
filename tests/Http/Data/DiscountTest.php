<?php

declare(strict_types=1);

namespace Tests\Http\Data;

use Mollie\Api\Http\Data\Discount;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DiscountTest extends TestCase
{
    #[Test]
    public function it_can_create_discount_from_array()
    {
        $object = Discount::fromArray($data = [
            'type' => 'percentage',
            'value' => '10',
        ]);

        $this->assertInstanceOf(Discount::class, $object);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $object->$key);
        }
    }
}
