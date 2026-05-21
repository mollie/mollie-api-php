<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use BackedEnum;
use BadMethodCallException;
use Mollie\Api\Contracts\QueryParameterBuilder;

abstract class QueryParameterSet implements QueryParameterBuilder
{
    /** @var array<string, string> */
    private array $values = [];

    /**
     * @return array<string, BackedEnum|string>
     */
    abstract protected static function options(): array;

    /**
     * @param  array<BackedEnum|string>  $values
     */
    final protected function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->values[$this->normalize($value)] = $this->normalize($value);
        }
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    final public static function __callStatic(string $name, array $arguments): static
    {
        return (new static)->withNamedOption($name);
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    final public function __call(string $name, array $arguments): static
    {
        return $this->withNamedOption($name);
    }

    final public static function from(BackedEnum|string ...$values): static
    {
        return new static($values);
    }

    final public function toQueryValue(): string
    {
        return implode(',', $this->values);
    }

    final public function __toString(): string
    {
        return $this->toQueryValue();
    }

    private function withNamedOption(string $name): static
    {
        $options = static::options();

        if (! array_key_exists($name, $options)) {
            throw new BadMethodCallException(sprintf('Call to undefined include/embed option %s::%s().', static::class, $name));
        }

        return $this->with($options[$name]);
    }

    private function with(BackedEnum|string $value): static
    {
        $clone = clone $this;
        $normalized = $this->normalize($value);
        $clone->values[$normalized] = $normalized;

        return $clone;
    }

    private function normalize(BackedEnum|string $value): string
    {
        return $value instanceof BackedEnum ? (string) $value->value : $value;
    }
}
