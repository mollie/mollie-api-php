<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetPaginatedChargebacksRequestFactory;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Utils\Utility;

class ChargebackEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a collection of Chargebacks from Mollie.
     *
     * @param  string  $from  The first chargeback ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): ChargebackCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedChargebacksRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        /** @var ChargebackCollection */
        return $this->send($request->test($testmode));
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
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetPaginatedChargebacksRequestFactory::new()
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
