<?php

namespace Tests\Factories;

use Mollie\Api\Factories\ApplicationFeeFactory;
use Mollie\Api\Http\Data\ApplicationFee;
use PHPUnit\Framework\TestCase;

class ApplicationFeeFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_application_fee_object()
    {
        $fee = ApplicationFeeFactory::new([
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
            'description' => 'Test application fee',
        ])->create();

        $this->assertInstanceOf(ApplicationFee::class, $fee);

        $this->assertEquals('EUR', $fee->amount->currency);
        $this->assertEquals('10.00', $fee->amount->value);
        $this->assertEquals('Test application fee', $fee->description);
    }
}
