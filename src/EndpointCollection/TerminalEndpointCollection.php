<?php

declare(strict_types=1);

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetTerminalPairingCodeRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedTerminalsRequest;
use Mollie\Api\Http\Requests\GetPaginatedTerminalPairingCodesRequest;
use Mollie\Api\Http\Requests\GetTerminalRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
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

    /**
     * Retrieves a collection of terminal pairing codes from Mollie.
     *
     * @param  string|null  $from  The first terminal pairing code ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function pairingCodesPage(?string $from = null, ?int $limit = null, array $filters = []): TerminalPairingCodeCollection
    {
        /** @var TerminalPairingCodeCollection */
        return $this->send(new GetPaginatedTerminalPairingCodesRequest(
            $from,
            $limit,
            $filters['sort'] ?? null,
            $filters['profileId'] ?? null,
        ));
    }

    /**
     * Create an iterator for iterating over terminal pairing codes retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function pairingCodesIterator(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->send(
            (new GetPaginatedTerminalPairingCodesRequest(
                $from,
                $limit,
                $filters['sort'] ?? null,
                $filters['profileId'] ?? null,
            ))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }

    /**
     * Retrieve a terminal pairing code from Mollie.
     *
     * @throws RequestException
     */
    public function getPairingCode(string $id, array $query = []): TerminalPairingCode
    {
        $request = GetTerminalPairingCodeRequestFactory::new($id)
            ->withQuery($query)
            ->create();

        /** @var TerminalPairingCode */
        return $this->send($request);
    }
}
