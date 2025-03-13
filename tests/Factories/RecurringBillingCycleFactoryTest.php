<?php

namespace Tests\Factories;

use Mollie\Api\Factories\RecurringBillingCycleFactory;
use Mollie\Api\Http\Data\RecurringBillingCycle;
use PHPUnit\Framework\TestCase;

class RecurringBillingCycleFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_recurring_billing_cycle_with_full_data()
    {
        $cycle = RecurringBillingCycleFactory::new([
            'interval' => '1 month',
            'descriptipn' => 'Monthly subscription',
            'amount' => [
                'currency' => 'EUR',
                'value' => '29.99',
            ],
            'times' => 12,
            'startDate' => '2024-03-01',
        ])->create();

        $this->assertInstanceOf(RecurringBillingCycle::class, $cycle);
    }

    /** @test */
    public function create_returns_recurring_billing_cycle_with_minimal_data()
    {
        $cycle = RecurringBillingCycleFactory::new([
            'interval' => '1 month',
            'descriptipn' => 'Monthly subscription',
        ])->create();

        $this->assertInstanceOf(RecurringBillingCycle::class, $cycle);
    }
}
