<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;

class ChargebackEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "chargebacks";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Chargeback::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = ChargebackCollection::class;

    /**
     * Retrieves a collection of Chargebacks from Mollie.
     *
     * @param string $from The first chargeback ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return ChargebackCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): ChargebackCollection
    {
        /** @var ChargebackCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over chargeback retrieved from Mollie.
     *
     * @param string $from The first chargevback ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
