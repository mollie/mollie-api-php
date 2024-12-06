<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;

class TerminalEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve terminal from Mollie.
     *
     * Will throw a ApiException if the terminal id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function get(string $id, $testmode = []): Terminal
    {
        $testmode = Helpers::extractBool($testmode, 'testmode');

        /** @var Terminal */
        return $this->send((new GetTerminalRequest($id))->test($testmode));
    }

    /**
     * Retrieves a collection of Terminals from Mollie for the current organization / profile, ordered from newest to oldest.
     *
     * @param  string|null  $from  The first terminal ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, $testmode = []): TerminalCollection
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        /** @var TerminalCollection */
        return $this->send((new GetPaginatedTerminalsRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over terminals retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        $testmode = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(
            (new GetPaginatedTerminalsRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
