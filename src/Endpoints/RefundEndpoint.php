<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class RefundEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "refunds";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Refund::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = RefundCollection::class;

    /**
     * Retrieves a collection of Refunds from Mollie.
     *
     * @param null|string $from The first refund ID you want to include in your list.
     * @param null|int $limit
     * @param array $parameters
     *
     * @return RefundCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): RefundCollection
    {
        /** @var RefundCollection */
        return $this->fetchCollection($from, $limit, $parameters);
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
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
