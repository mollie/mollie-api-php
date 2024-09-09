<?php

namespace Tests\Traits;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Contracts\DataResolver;
use Mollie\Api\Traits\ResolvesValues;
use PHPUnit\Framework\TestCase;

class ResolvesValuesTest extends TestCase
{
    /** @test */
    public function resolve_returns_array_of_resolved_values()
    {
        $baz = new Baz('baz');
        $bar = new Bar($baz);
        $fooData = new FooData($bar, 'foo');

        $this->assertEquals([
            'name' => 'foo',
            'bar' => [
                'baz' => [
                    'value' => 'baz',
                ],
            ],
        ], $fooData->resolve());
    }
}

class FooData implements Arrayable
{
    use ResolvesValues;

    public Bar $bar;

    public string $name;

    public function __construct(
        Bar $bar,
        string $name,
    ) {
        $this->bar = $bar;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'bar' => $this->bar,
        ];
    }
}

class Bar implements DataResolver
{
    use ResolvesValues;

    public Baz $baz;

    public function __construct(Baz $baz)
    {
        $this->baz = $baz;
    }

    public function data(): array
    {
        return [
            'baz' => $this->baz,
        ];
    }
}

class Baz implements DataProvider
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function data(): array
    {
        return [
            'value' => $this->value,
        ];
    }
}
