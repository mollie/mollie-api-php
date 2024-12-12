<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Data\SortablePaginatedQuery;
use Mollie\Api\Http\Requests\GetPaginatedSettlementPaymentsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Settlement;

class SettlementPaymentEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Payments from Mollie.
     *
     * @param  array|SortablePaginatedQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Settlement $settlement, $query = [], bool $testmode = false): PaymentCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Payments from Mollie.
     *
     * @param  array|SortablePaginatedQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $settlementId, $query = [], bool $testmode = false): PaymentCollection
    {
        if (! $query instanceof SortablePaginatedQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = SortablePaginatedQueryFactory::new($query)->create();
        }

        return $this->send((new GetPaginatedSettlementPaymentsRequest($settlementId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over payments for the given settlement, retrieved from Mollie.
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

    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($parameters, 'testmode', false);
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $parameters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementPaymentsRequest($settlementId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
