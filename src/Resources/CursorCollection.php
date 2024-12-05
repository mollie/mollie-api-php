<?php

namespace Mollie\Api\Resources;

use Generator;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;

abstract class CursorCollection extends ResourceCollection
{
    private bool $autoHydrate = false;

    public function setAutoHydrate(bool $shouldAutoHydrate = true): void
    {
        $this->autoHydrate = $shouldAutoHydrate;
    }

    public function shouldAutoHydrate(): bool
    {
        if ($this->response === null) {
            return $this->autoHydrate;
        }

        $request = $this->response->getRequest();

        /**
         * Don't try to hydrate when the request
         * already has auto-hydration enabled. The
         * Hydrate Middleware will take care of that.
         */
        if ($request instanceof ResourceHydratableRequest && $request->shouldAutoHydrate()) {
            return false;
        }

        return $this->autoHydrate;
    }

    /**
     * Return the next set of resources when available
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function next(): ?CursorCollection
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
    public function previous(): ?CursorCollection
    {
        if (! $this->hasPrevious()) {
            return null;
        }

        return $this->fetchCollection($this->_links->previous->href);
    }

    private function fetchCollection(string $url): CursorCollection
    {
        $response = $this
            ->connector
            ->send(new DynamicGetRequest($url, static::class));

        return $this->shouldAutoHydrate()
            ? $response->toResource()
            : $response;
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
                $page->setAutoHydrate();
                
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
