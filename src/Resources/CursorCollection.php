<?php

namespace Mollie\Api\Resources;

use Generator;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\ResponseStatusCode;
use Mollie\Api\MollieApiClient;

abstract class CursorCollection extends BaseCollection
{
    protected MollieApiClient $client;

    /**
     * @param MollieApiClient $client
     * @param int $count
     * @param \stdClass|null $_links
     */
    final public function __construct(MollieApiClient $client, int $count, ?\stdClass $_links)
    {
        parent::__construct($count, $_links);

        $this->client = $client;
    }

    /**
     * @return BaseResource
     */
    abstract protected function createResourceObject();

    /**
     * Return the next set of resources when available
     *
     * @return CursorCollection|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    final public function next(): ?CursorCollection
    {
        if (!$this->hasNext()) {
            return null;
        }

        return $this->fetchCollection($this->_links->next->href);
    }

    /**
     * Return the previous set of resources when available
     *
     * @return CursorCollection|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    final public function previous(): ?CursorCollection
    {
        if (!$this->hasPrevious()) {
            return null;
        }

        return $this->fetchCollection($this->_links->previous->href);
    }

    private function fetchCollection(string $url): CursorCollection
    {
        $response = $this
            ->client
            ->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $url);

        if ($response->status() !== ResponseStatusCode::HTTP_OK) {
            throw new ApiException($response->body(), $response->status());
        }

        $data = $response->decode();

        $collection = new static($this->client, $data->count, $data->_links);

        foreach ($data->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, $this->createResourceObject());
        }

        return $collection;
    }

    /**
     * Determine whether the collection has a next page available.
     *
     * @return bool
     */
    public function hasNext(): bool
    {
        return isset($this->_links->next->href);
    }

    /**
     * Determine whether the collection has a previous page available.
     *
     * @return bool
     */
    public function hasPrevious(): bool
    {
        return isset($this->_links->previous->href);
    }

    /**
     * Iterate over a CursorCollection and yield its elements.
     *
     * @param bool $iterateBackwards
     *
     * @return LazyCollection
     */
    public function getAutoIterator(bool $iterateBackwards = false): LazyCollection
    {
        $page = $this;

        return new LazyCollection(function () use ($page, $iterateBackwards): Generator {
            while (true) {
                foreach ($page as $item) {
                    yield $item;
                }

                if (($iterateBackwards && !$page->hasPrevious()) || !$page->hasNext()) {
                    break;
                }

                $page = $iterateBackwards
                    ? $page->previous()
                    : $page->next();
            }
        });
    }
}
