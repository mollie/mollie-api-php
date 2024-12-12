<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\DataResolver;
use Mollie\Api\Traits\ResolvesValues;

/**
 * @template T of \Mollie\Api\Contracts\DataResolver
 */
class DataCollection implements Arrayable, DataResolver
{
    use ResolvesValues;

    /**
     * @var array<T>
     */
    public array $items;

    /**
     * @param  array<T>  $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function wrap(object $subject): self
    {
        if ($subject instanceof static) {
            return $subject;
        }

        if ($subject instanceof Arrayable) {
            return new static($subject->toArray());
        }

        return new static((array) $subject);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function map(callable $callback): self
    {
        return new static(array_map($callback, $this->items));
    }

    public function filter(): self
    {
        return new static(array_filter($this->items));
    }
}