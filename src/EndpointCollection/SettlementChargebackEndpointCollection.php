<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedSettlementChargebacksQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Data\GetPaginatedSettlementChargebacksQuery;
use Mollie\Api\Http\Requests\GetPaginatedSettlementChargebacksRequest;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;

class SettlementChargebackEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Chargebacks from Mollie.
     *
     * @param  array|GetPaginatedSettlementChargebacksQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Settlement $settlement, $query = [], bool $testmode = false): ChargebackCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Chargebacks from Mollie.
     *
     * @param  array|GetPaginatedSettlementChargebacksQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $settlementId, $query = [], bool $testmode = false): ChargebackCollection
    {
        if (! $query instanceof GetPaginatedSettlementChargebacksQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaginatedSettlementChargebacksQueryFactory::new($query)->create();
        }

        /** @var ChargebackCollection */
        return $this->send((new GetPaginatedSettlementChargebacksRequest($settlementId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over chargebacks for the given settlement, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(
        Settlement $settlement,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($settlement->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over chargebacks for the given settlement id, retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = GetPaginatedSettlementChargebacksQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementChargebacksRequest($settlementId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
