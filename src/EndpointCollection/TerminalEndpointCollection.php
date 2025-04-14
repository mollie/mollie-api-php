<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use Mollie\Api\Utils\Utility;

class TerminalEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve terminal from Mollie.
     *
     * Will throw a ApiException if the terminal id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function get(string $id, $testmode = false): Terminal
    {
        $testmode = Utility::extractBool($testmode, 'testmode');

        /** @var Terminal */
        return $this->send((new GetTerminalRequest($id))->test($testmode));
    }

    /**
     * Retrieves a collection of Terminals from Mollie for the current organization / profile, ordered from newest to oldest.
     *
     * @param  string|null  $from  The first terminal ID you want to include in your list.
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, $testmode = false): TerminalCollection
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var TerminalCollection */
        return $this->send((new GetPaginatedTerminalsRequest($from, $limit))->test($testmode));
    }

    /**
     * Create an iterator for iterating over terminals retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     * @param  bool|array  $testmode
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        $testmode = false,
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        return $this->send(
            (new GetPaginatedTerminalsRequest($from, $limit))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
