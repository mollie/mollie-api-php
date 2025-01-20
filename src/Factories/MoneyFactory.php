<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Money;

class MoneyFactory extends OldFactory
{
    public function create(): Money
    {
        if (! $this->has(['currency', 'value'])) {
            throw new \InvalidArgumentException('Invalid Money data provided');
        }

        return new Money(
            $this->get('currency'),
            $this->get('value')
        );
    }
}
