<?php

namespace Tests\Utils;

use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    /** @test */
    public function get(): void
    {
        $array = ['foo' => ['bar' => 'baz']];

        $this->assertEquals('baz', Arr::get($array, 'foo.bar'));
        $this->assertEquals(null, Arr::get($array, 'foo.baz'));
        $this->assertEquals('default', Arr::get($array, 'foo.baz', 'default'));
    }

    /** @test */
    public function pull(): void
    {
        $array = ['foo' => ['bar' => 'baz']];

        $this->assertEquals('baz', Arr::pull($array, 'foo.bar'));
        $this->assertEquals(['foo' => []], $array);
    }

    /** @test */
    public function except(): void
    {
        $array = ['foo' => 'bar', 'baz' => 'qux'];

        $this->assertEquals(['foo' => 'bar'], Arr::except($array, ['baz']));
    }

    /** @test */
    public function forget(): void
    {
        $array = ['foo' => ['bar' => 'baz']];

        Arr::forget($array, 'foo.bar');

        $this->assertEquals(['foo' => []], $array);
    }

    /** @test */
    public function has(): void
    {
        $array = ['foo' => ['bar' => 'baz']];

        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'foo.bar'));
        $this->assertFalse(Arr::has($array, 'foo.baz'));
        $this->assertFalse(Arr::has($array, 'baz'));
    }

    /** @test */
    public function exists(): void
    {
        $array = ['foo' => 'bar'];

        $this->assertTrue(Arr::exists($array, 'foo'));
        $this->assertFalse(Arr::exists($array, 'bar'));
    }

    /** @test */
    public function join(): void
    {
        $array = ['foo', 'bar', 'baz'];

        $this->assertEquals('foo, bar, baz', Arr::join($array));
        $this->assertEquals('foo-bar-baz', Arr::join($array, '-'));
    }

    /** @test */
    public function wrap(): void
    {
        $value = 'foo';

        $this->assertEquals(['foo'], Arr::wrap($value));

        $array = ['foo', 'bar'];

        $this->assertEquals($array, Arr::wrap($array));
    }

    /** @test */
    public function map(): void
    {
        // Test with a callback that only needs the value
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = Arr::map($array, function ($value) {
            return $value * 2;
        });
        $this->assertEquals(['a' => 2, 'b' => 4, 'c' => 6], $result);

        // Test with a callback that uses both value and key
        $result = Arr::map($array, function ($value, $key) {
            return $key . $value;
        });
        $this->assertEquals(['a' => 'a1', 'b' => 'b2', 'c' => 'c3'], $result);

        // Test with associative array
        $assocArray = ['first' => 'John', 'last' => 'Doe'];
        $result = Arr::map($assocArray, function ($value) {
            return strtoupper($value);
        });
        $this->assertEquals(['first' => 'JOHN', 'last' => 'DOE'], $result);

        // Test with empty array
        $emptyArray = [];
        $result = Arr::map($emptyArray, function ($value) {
            return $value * 2;
        });
        $this->assertEquals([], $result);
    }

    /** @test */
    public function includes(): void
    {
        $array = ['includes' => ['payment']];

        $this->assertTrue(Arr::includes($array, 'includes', 'payment'));
        $this->assertFalse(Arr::includes($array, 'includes', 'refund'));
    }
}
