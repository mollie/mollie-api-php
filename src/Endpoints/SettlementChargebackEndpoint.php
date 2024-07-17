<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;

class SettlementChargebackEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "settlements_chargebacks";

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
     * Retrieves a collection of Settlement Chargebacks from Mollie.
     *
     * @param string $settlementId
     * @param string|null $from The first chargeback ID you want to include in your list.
     * @param int|null $limit
     * @param array $parameters
     *
     * @return ChargebackCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $settlementId, ?string $from = null, ?int $limit = null, array $parameters = []): ChargebackCollection
    {
        $this->parentId = $settlementId;

        /** @var ChargebackCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over chargeback for the given settlement id, retrieved from Mollie.
     *
     * @param string $settlementId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $settlementId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
