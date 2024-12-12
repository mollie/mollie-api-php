<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedSettlementRefundsQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Data\GetPaginatedSettlementRefundsQuery;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRefundsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\Settlement;

class SettlementRefundEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Refunds from Mollie.
     *
     * @param  array|GetPaginatedSettlementRefundsQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Settlement $settlement, $query = [], bool $testmode = false): RefundCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Refunds from Mollie.
     *
     * @param  array|GetPaginatedSettlementRefundsQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $settlementId, $query = [], bool $testmode = false): RefundCollection
    {
        if (! $query instanceof GetPaginatedSettlementRefundsQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaginatedSettlementRefundsQueryFactory::new($query)->create();
        }

        return $this->send((new GetPaginatedSettlementRefundsRequest($settlementId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over refunds for the given settlement, retrieved from Mollie.
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
     * Create an iterator for iterating over refunds for the given settlement id, retrieved from Mollie.
     */
    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = GetPaginatedSettlementRefundsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementRefundsRequest($settlementId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
