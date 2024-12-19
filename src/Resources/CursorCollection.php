<?php

namespace Mollie\Api\Resources;

use Generator;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;

abstract class CursorCollection extends ResourceCollection
{
    /**
     * Return the next set of resources when available
     *
     * @return null|CursorCollection|Response
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function next()
    {
        if (! $this->hasNext()) {
            return null;
        }

        return $this->fetchCollection($this->_links->next->href);
    }

    /**
     * Return the previous set of resources when available
     *
     * @return null|CursorCollection|Response
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function previous()
    {
        if (! $this->hasPrevious()) {
            return null;
        }

        return $this->fetchCollection($this->_links->previous->href);
    }

    /**
     * @return CursorCollection|Response
     */
    private function fetchCollection(string $url)
    {
        return $this
            ->connector
            ->send((new DynamicGetRequest($url))->setHydratableResource(static::class));
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

        return (new LazyCollection(function () use ($page, $iterateBackwards): Generator {
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
        }))->setResponse($this->response);
    }
}
