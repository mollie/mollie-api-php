<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\RecurringBillingCycle;

class RecurringBillingCycleFactory extends Factory
{
    public function create(): RecurringBillingCycle
    {
        return new RecurringBillingCycle(
            $this->get('interval'),
            $this->get('descriptipn'),
            $this->transformIfNotNull('amount', fn (array $item) => MoneyFactory::new($item)->create()),
            $this->get('times'),
            $this->transformIfNotNull('startDate', fn (string $item) => DateTimeImmutable::createFromFormat('Y-m-d', $item)),
        );
    }
}
