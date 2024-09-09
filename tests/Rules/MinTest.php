<?php

namespace Tests\Rules;

use Mollie\Api\Rules\Min;
use PHPUnit\Framework\TestCase;

class MinTest extends TestCase
{
    /** @test */
    public function value_method_returns_instance()
    {
        $min = 10;
        $rule = Min::value($min);

        $this->assertInstanceOf(Min::class, $rule);
    }

    /** @test */
    public function validate_invokes_callback_when_numeric_value_is_less_than_min()
    {
        $min = 10;
        $rule = new Min($min);

        $rule->validate(5, null, function ($message) use ($min) {
            $this->assertEquals("The value must be at least {$min}.", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_when_numeric_value_is_equal_to_or_greater_than_min()
    {
        $min = 10;
        $rule = new Min($min);

        $rule->validate(10, null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $rule->validate(15, null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $this->assertTrue(true);
    }

    /** @test */
    public function validate_invokes_callback_when_string_length_is_less_than_min()
    {
        $min = 5;
        $rule = new Min($min);

        $rule->validate('tiny', null, function ($message) use ($min) {
            $this->assertEquals("The value must be at least {$min}.", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_when_string_length_is_equal_to_or_greater_than_min()
    {
        $min = 5;
        $rule = new Min($min);

        $rule->validate('valid', null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $rule->validate('longer', null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $this->assertTrue(true);
    }
}
