<?php

namespace Tests\Mollie\TestHelpers;

trait AmountObjectTestHelpers
{
    protected function assertAmountObject($value, $currency, $amountObject)
    {
        $this->assertEquals(
            $this->createAmountObject($value, $currency),
            $amountObject
        );
    }

    protected function createAmountObject($value, $currency)
    {
        return (object) [
            'value' => $value,
            'currency' => $currency,
        ];
    }
}
