<?php

namespace Tests\Rules;

use Mollie\Api\Rules\Matches;
use PHPUnit\Framework\TestCase;

class MatchesTest extends TestCase
{
    /** @test */
    public function pattern_method_returns_instance()
    {
        $pattern = '/^test$/';
        $rule = Matches::pattern($pattern);

        $this->assertInstanceOf(Matches::class, $rule);
    }

    /** @test */
    public function validate_invokes_callback_on_non_matching_value()
    {
        $pattern = '/^test$/';
        $rule = new Matches($pattern);

        $rule->validate('nonmatching', null, function ($message) use ($pattern) {
            $this->assertEquals("The value nonmatching does not match the pattern: {$pattern}", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_on_matching_value()
    {
        $pattern = '/^test$/';
        $rule = new Matches($pattern);

        $rule->validate('test', null, function () {
            $this->fail('Callback should not be invoked.');
        });

        $this->assertTrue(true);
    }
}
