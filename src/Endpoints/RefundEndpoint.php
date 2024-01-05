<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class RefundEndpoint extends CollectionRestEndpoint
{
    protected string $resourcePath = "refunds";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Refund
    {
        return new Refund($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): RefundCollection
    {
        return new RefundCollection($this->client, $count, $_links);
    }

    /**
     * Retrieves a collection of Refunds from Mollie.
     *
     * @param string $from The first refund ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return RefundCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?string $limit = null, array $parameters = []): RefundCollection
    {
        return $this->rest_list($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over refunds retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->rest_iterator($from, $limit, $parameters, $iterateBackwards);
    }
}
