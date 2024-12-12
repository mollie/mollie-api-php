<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedRefundsQueryFactory;
use Mollie\Api\Utils\Utility;
use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\RefundCollection;

class RefundEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Refunds from Mollie.
     *
     * @param  string|null  $from  The first refund ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): RefundCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $query = GetPaginatedRefundsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var RefundCollection */
        return $this->send((new GetPaginatedRefundsRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over refunds retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $query = GetPaginatedRefundsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedRefundsRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
