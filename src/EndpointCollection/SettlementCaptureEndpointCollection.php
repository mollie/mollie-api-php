<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedSettlementCapturesQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Query\GetPaginatedSettlementCapturesQuery;
use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;

class SettlementCaptureEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @param  array|GetPaginatedSettlementCapturesQuery  $query
     *
     * @throws ApiException
     */
    public function pageFor(Settlement $settlement, $query = [], bool $testmode = false): CaptureCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @param  array|GetPaginatedSettlementCapturesQuery  $query
     *
     * @throws ApiException
     */
    public function pageForId(string $settlementId, $query = [], bool $testmode = false): CaptureCollection
    {
        if (! $query instanceof GetPaginatedSettlementCapturesQuery) {
            $testmode = Helpers::extractBool($query, 'testmode', $testmode);
            $query = GetPaginatedSettlementCapturesQueryFactory::new($query)->create();
        }

        /** @var CaptureCollection */
        return $this->send((new GetPaginatedSettlementCapturesRequest($settlementId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over captures for the given settlement, retrieved from Mollie.
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
     * Create an iterator for iterating over captures for the given settlement id, retrieved from Mollie.
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
        $query = GetPaginatedSettlementCapturesQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementCapturesRequest($settlementId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
