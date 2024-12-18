<?php

namespace Tests\Utils;

use DateTimeImmutable;
use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Http\Data\Data;
use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;
use Stringable;

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
    public function includes(): void
    {
        $array = ['includes' => ['payment']];

        $this->assertTrue(Arr::includes($array, 'includes', 'payment'));
        $this->assertFalse(Arr::includes($array, 'includes', 'refund'));
    }

    /** @test */
    public function resolve(): void
    {
        $foo = new Foo('bar', new Bar('baz'));
        $anyData = new AnyData(['foo' => $foo]);

        $this->assertEquals(['foo' => ['foo' => 'bar', 'bar' => 'baz']], Arr::resolve($anyData));

        $nullResult = Arr::resolve(null);
        $this->assertEquals([], $nullResult);

        $resolvesDateTime = Arr::resolve(['dateTime' => DateTimeImmutable::createFromFormat('Y-m-d', '2024-01-01')]);
        $this->assertEquals(['dateTime' => '2024-01-01'], $resolvesDateTime);

        $filtersResult = Arr::resolve(['some' => null, 'bar' => 'baz']);
        $this->assertEquals(['bar' => 'baz'], $filtersResult);
    }
}

class Foo extends Data
{
    public string $foo;

    public Bar $bar;

    public function __construct(string $foo, Bar $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function toArray(): array
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
        ];
    }
}

class Bar implements Stringable
{
    public string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }

    public function __toString(): string
    {
        return $this->bar;
    }
}
