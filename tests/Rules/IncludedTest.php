<?php

namespace Tests\Rules;

use Mollie\Api\Rules\Included;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class IncludedTest extends TestCase
{
    /** @test */
    public function in_method_creates_instance_with_class_constants()
    {
        $rule = Included::in(SampleClass::class);

        $expectedAllowedValues = [
            'VALUE1' => 'value1',
            'VALUE2' => 'value2',
        ];

        $this->assertInstanceOf(Included::class, $rule);
        $this->assertEquals($expectedAllowedValues, (new ReflectionClass(SampleClass::class))->getConstants());
    }

    /** @test */
    public function validate_invokes_callback_when_value_not_included()
    {
        $allowed = ['value1', 'value2'];
        $rule = new Included($allowed);

        $rule->validate('invalid_value', null, function ($message) use ($allowed) {
            $this->assertEquals("Invalid include: 'invalid_value'. Allowed are: ".implode(', ', $allowed).'.', $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_when_value_is_included()
    {
        $allowed = ['value1', 'value2'];
        $rule = new Included($allowed);

        $rule->validate('value1', null, function () {
            $this->fail('Callback should not be invoked for a valid value.');
        });

        $rule->validate('value2', null, function () {
            $this->fail('Callback should not be invoked for a valid value.');
        });

        $rule->validate('value1,value2', null, function () {
            $this->fail('Callback should not be invoked for a valid value.');
        });

        $this->assertTrue(true);
    }
}

class SampleClass
{
    const VALUE1 = 'value1';

    const VALUE2 = 'value2';
}
