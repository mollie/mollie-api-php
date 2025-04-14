<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedSettlementRefundsQueryFactory;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Utils\Utility;

class SettlementRefundEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Refunds from Mollie.
     *
     * @throws RequestException
     */
    public function pageFor(Settlement $settlement, array $query = [], bool $testmode = false): RefundCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Refunds from Mollie.
     *
     * @throws RequestException
     */
    public function pageForId(string $settlementId, array $query = [], bool $testmode = false): RefundCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaginatedSettlementRefundsQueryFactory::new($settlementId)
            ->withQuery($query)
            ->create();

        return $this->send($request->test($testmode));
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
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedSettlementRefundsQueryFactory::new($settlementId)
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
                ->test($testmode)
        );
    }
}
