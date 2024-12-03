<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetPaginatedSettlementRequest;
use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;

class SettlementEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single settlement from Mollie.
     *
     * Will throw an ApiException if the settlement id is invalid or the resource cannot be found.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function get(string $settlementId, $testmode = []): Settlement
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Settlement */
        return $this->send((new GetSettlementRequest($settlementId))->test($testmode));
    }

    /**
     * Retrieve the next settlement from Mollie.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function next($testmode = []): ?Settlement
    {
        return $this->get('next', $testmode);
    }

    /**
     * Retrieve the open balance from Mollie.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function open($testmode = []): ?Settlement
    {
        return $this->get('open', $testmode);
    }

    /**
     * Retrieve a collection of settlements from Mollie.
     *
     * @param  string|null  $from  The first settlement ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): SettlementCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var SettlementCollection */
        return $this->send((new GetPaginatedSettlementRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over settlements retrieved from Mollie.
     *
     * @param  string|null  $from  The first settlement ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSettlementRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
