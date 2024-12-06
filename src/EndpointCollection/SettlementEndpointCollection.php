<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedSettlementsQueryFactory;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;

class SettlementEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single settlement from Mollie.
     *
     * Will throw a ApiException if the settlement id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function get(string $settlementId): Settlement
    {
        return $this->send(new GetSettlementRequest($settlementId));
    }

    /**
     * Retrieve the details of the current settlement that has not yet been paid out.
     *
     * @throws ApiException
     */
    public function next(): Settlement
    {
        return $this->send(new GetSettlementRequest('next'));
    }

    /**
     * Retrieve the details of the open balance of the organization.
     *
     * @throws ApiException
     */
    public function open(): Settlement
    {
        return $this->send(new GetSettlementRequest('open'));
    }

    /**
     * Retrieves a collection of Settlements from Mollie.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): SettlementCollection
    {
        $query = GetPaginatedSettlementsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(new GetPaginatedSettlementsRequest($query));
    }

    /**
     * Create an iterator for iterating over settlements retrieved from Mollie.
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $query = GetPaginatedSettlementsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementsRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
