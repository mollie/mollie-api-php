<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedChargebackQueryFactory;
use Mollie\Api\Http\Requests\GetPaginatedChargebacksRequest;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;

class ChargebackEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Chargebacks from Mollie.
     *
     * @param  string  $from  The first chargeback ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): ChargebackCollection
    {
        $query = GetPaginatedChargebackQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var ChargebackCollection */
        return $this->send(new GetPaginatedChargebacksRequest($query));
    }

    /**
     * Create an iterator for iterating over chargeback retrieved from Mollie.
     *
     * @param  string  $from  The first chargevback ID you want to include in your list.
     * @param  array  $filters
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $query = GetPaginatedChargebackQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedChargebacksRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
