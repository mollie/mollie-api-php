<?php

namespace Mollie\Api\Resources;

use Iterator;
use IteratorAggregate;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Traits\HasResponse;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements IteratorAggregate<TKey, TValue>
 */
class LazyCollection implements IsResponseAware, IteratorAggregate
{
    use HasResponse;

    /**
     * @var callable
     */
    private $source;

    /**
     * @param  callable  $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * Get all items in the collection.
     */
    public function all(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  TKey  $key
     * @return TValue|null
     */
    public function get($key)
    {
        foreach ($this as $outerKey => $outerValue) {
            if ($outerKey == $key) {
                return $outerValue;
            }
        }

        return null;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)  $callback
     */
    public function filter(callable $callback): self
    {
        return (new self(function () use ($callback) {
            foreach ($this as $key => $value) {
                if ($callback($value, $key)) {
                    yield $key => $value;
                }
            }
        }))->setResponse($this->response);
    }

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return TValue|null
     */
    public function first(?callable $callback = null)
    {
        $iterator = $this->getIterator();

        if (is_null($callback)) {
            if (! $iterator->valid()) {
                return null;
            }

            return $iterator->current();
        }

        foreach ($iterator as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): self
    {
        return (new self(function () use ($callback) {
            foreach ($this as $key => $value) {
                yield $key => $callback($value, $key);
            }
        }))->setResponse($this->response);
    }

    /**
     * Take the first {$limit} items.
     *
     * @return static
     */
    public function take(int $limit): self
    {
        return (new self(function () use ($limit) {
            $iterator = $this->getIterator();

            while ($limit--) {
                if (! $iterator->valid()) {
                    break;
                }

                yield $iterator->key() => $iterator->current();

                if ($limit) {
                    $iterator->next();
                }
            }
        }))->setResponse($this->response);
    }

    /**
     * Determine if all items pass the given truth test.
     *
     * @param  (callable(TValue, TKey): bool)  $callback
     */
    public function every(callable $callback): bool
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $key => $value) {
            if (! $callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Count the number of items in the collection.
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * Get an iterator for the items.
     *
     * @return Iterator<TKey, TValue>
     */
    public function getIterator(): Iterator
    {
        return $this->makeIterator($this->source);
    }

    /**
     * Get an iterator for the given value.
     *
     * @template TIteratorKey of array-key
     * @template TIteratorValue
     *
     * @param  IteratorAggregate<TIteratorValue>|(callable(): \Generator<TIteratorKey, TIteratorValue>)  $source
     * @return Iterator<TIteratorValue>
     */
    protected function makeIterator($source): Iterator
    {
        if ($source instanceof IteratorAggregate) {
            return $source->getIterator();
        }

        return $source();
    }
}
