<?php

namespace Mollie\Api\Http\Data;

use Countable;
use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Utils\Arr;

/**
 * @template T of mixed
 */
class DataCollection implements Resolvable, Countable
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
