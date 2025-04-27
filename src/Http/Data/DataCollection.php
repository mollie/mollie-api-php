<?php

namespace Mollie\Api\Http\Data;

use Countable;
use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Utils\Arr;

/**
 * @template T of mixed
 */
class DataCollection implements Countable, Resolvable
{
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

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param  mixed  $subject
     */
    public static function wrap($subject): self
    {
        if ($subject instanceof static) {
            return $subject;
        }

        if ($subject instanceof Arrayable) {
            return new static($subject->toArray());
        }

        return new static(Arr::wrap($subject));
    }

    public function values(): self
    {
        return new static(array_values($this->items));
    }

    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }

    public function map(callable $callback): self
    {
        return new static(Arr::map($this->items, $callback));
    }

    public function filter($callback = null): self
    {
        /**
         * PHP 7.4 and below does not support nullable callbacks.
         */
        if ($callback === null) {
            return new static(array_filter($this->items));
        }

        return new static(array_filter($this->items, $callback));
    }
}
