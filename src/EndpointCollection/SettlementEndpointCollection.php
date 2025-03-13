<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedSettlementsRequestFactory;
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
     * @throws RequestException
     */
    public function get(string $settlementId): Settlement
    {
        return $this->send(new GetSettlementRequest($settlementId));
    }

    /**
     * Retrieve the details of the current settlement that has not yet been paid out.
     *
     * @throws RequestException
     */
    public function next(): Settlement
    {
        return $this->send(new GetSettlementRequest('next'));
    }

    /**
     * Retrieve the details of the open balance of the organization.
     *
     * @throws RequestException
     */
    public function open(): Settlement
    {
        return $this->send(new GetSettlementRequest('open'));
    }

    /**
     * Retrieves a collection of Settlements from Mollie.
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): SettlementCollection
    {
        $request = GetPaginatedSettlementsRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send($request);
    }

    /**
     * Create an iterator for iterating over settlements retrieved from Mollie.
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $request = GetPaginatedSettlementsRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
