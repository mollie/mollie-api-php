<?php

namespace Tests\Rules;

use Mollie\Api\Rules\Max;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{
    /** @test */
    public function value_method_returns_instance()
    {
        $max = 100;
        $rule = Max::value($max);

        $this->assertInstanceOf(Max::class, $rule);
    }

    /** @test */
    public function validate_invokes_callback_when_numeric_value_exceeds_max()
    {
        $max = 100;
        $rule = new Max($max);

        $rule->validate(150, null, function ($message) use ($max) {
            $this->assertEquals("The value must not exceed {$max}.", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_when_numeric_value_is_equal_to_or_less_than_max()
    {
        $max = 100;
        $rule = new Max($max);

        // If no exception or callback is triggered, the test passes
        $rule->validate(100, null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $rule->validate(50, null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $this->assertTrue(true);
    }

    /** @test */
    public function validate_invokes_callback_when_string_length_exceeds_max()
    {
        $max = 5;
        $rule = new Max($max);

        $rule->validate('exceeds', null, function ($message) use ($max) {
            $this->assertEquals("The value must not exceed {$max}.", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_when_string_length_is_equal_to_or_less_than_max()
    {
        $max = 5;
        $rule = new Max($max);

        $rule->validate('valid', null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $rule->validate('tiny', null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $this->assertTrue(true);
    }

    /** @test */
    public function validate_invokes_callback_when_string_value_exceeds_max()
    {
        $max = 7;
        $rule = new Max($max);

        $rule->validate('DE123124', null, function ($message) use ($max) {
            $this->assertEquals("The value must not exceed {$max}.", $message);
        });
    }
}
