<?php

namespace Tests\Helpers;

use Mollie\Api\Helpers\Arr;
use Tests\TestCase;

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
    public function includes(): void
    {
        $array = ['includes' => ['payment']];

        $this->assertTrue(Arr::includes($array, 'includes', 'payment'));
        $this->assertFalse(Arr::includes($array, 'includes', 'refund'));
    }
}
