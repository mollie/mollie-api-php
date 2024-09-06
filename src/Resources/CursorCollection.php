<?php

namespace Mollie\Api\Resources;

use Generator;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Traits\InteractsWithResource as TraitsInteractsWithResource;

abstract class CursorCollection extends BaseCollection
{
    use TraitsInteractsWithResource;

    /**
     * Return the next set of resources when available
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    final public function next(): ?CursorCollection
    {
        if (! $this->hasNext()) {
            return null;
        }

        return $this->fetchCollection($this->_links->next->href);
    }

    /**
     * Return the previous set of resources when available
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    final public function previous(): ?CursorCollection
    {
        if (! $this->hasPrevious()) {
            return null;
        }

        return $this->fetchCollection($this->_links->previous->href);
    }

    private function fetchCollection(string $url): CursorCollection
    {
        return $this
            ->connector
            ->send(new DynamicGetRequest($url, static::class));
    }

    /**
     * Determine whether the collection has a next page available.
     */
    public function hasNext(): bool
    {
        return isset($this->_links->next->href);
    }

    /**
     * Determine whether the collection has a previous page available.
     */
    public function hasPrevious(): bool
    {
        return isset($this->_links->previous->href);
    }

    /**
     * Iterate over a CursorCollection and yield its elements.
     */
    public function getAutoIterator(bool $iterateBackwards = false): LazyCollection
    {
        $page = $this;

        return new LazyCollection(function () use ($page, $iterateBackwards): Generator {
            while (true) {
                foreach ($page as $item) {
                    yield $item;
                }

                if (($iterateBackwards && ! $page->hasPrevious()) || ! $page->hasNext()) {
                    break;
                }

                $page = $iterateBackwards
                    ? $page->previous()
                    : $page->next();
            }
        });
    }
}
