<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedSettlementCapturesRequestFactory;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Utils\Utility;

class SettlementCaptureEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @throws RequestException
     */
    public function pageFor(Settlement $settlement, array $query = [], bool $testmode = false): CaptureCollection
    {
        return $this->pageForId($settlement->id, $query, $testmode);
    }

    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @throws RequestException
     */
    public function pageForId(string $settlementId, array $query = [], bool $testmode = false): CaptureCollection
    {
        $testmode = Utility::extractBool($query, 'testmode', $testmode);

        $request = GetPaginatedSettlementCapturesRequestFactory::new($settlementId)
            ->withQuery($query)
            ->create();

        /** @var CaptureCollection */
        return $this->send($request->test($testmode));
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
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedSettlementCapturesRequestFactory::new($settlementId)
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
