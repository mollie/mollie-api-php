<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\Requests\GetPaginatedTerminalPairingCodesRequest;
use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Http\Requests\RequestTerminalPairingCodeRequest;
use Mollie\Api\Http\Requests\RevokeTerminalPairingCodeRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Resources\TerminalPairingCodeCollection;

class TerminalPairingCodeEndpointCollection extends EndpointCollection
{
    /**
     * Request a new pairing code to onboard a point-of-sale terminal.
     *
     * @throws RequestException
     */
    public function request(string $profileId, bool $includeQrCode = false): TerminalPairingCode
    {
        /** @var TerminalPairingCode */
        return $this->send(new RequestTerminalPairingCodeRequest($profileId, $includeQrCode));
    }

    /**
     * Retrieve a terminal pairing code from Mollie.
     *
     * Will throw an ApiException if the pairing code ID is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $id, bool $includeQrCode = false): TerminalPairingCode
    {
        /** @var TerminalPairingCode */
        return $this->send(new GetTerminalPairingCodeRequest($id, $includeQrCode));
    }

    /**
     * Retrieve a collection of terminal pairing codes from Mollie, ordered from newest to oldest.
     *
     * @param  string|null  $from  The first pairing code ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function page(
        ?string $from = null,
        ?int $limit = null,
        ?string $profileId = null,
        ?string $sort = null
    ): TerminalPairingCodeCollection {
        /** @var TerminalPairingCodeCollection */
        return $this->send(new GetPaginatedTerminalPairingCodesRequest($from, $limit, $profileId, $sort));
    }

    /**
     * Revoke a terminal pairing code, preventing onboarding of new terminals.
     *
     * Terminals that have already paired with this code are not affected.
     *
     * Will throw an ApiException if the pairing code ID is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function revoke(string $id): TerminalPairingCode
    {
        /** @var TerminalPairingCode */
        return $this->send(new RevokeTerminalPairingCodeRequest($id));
    }

    /**
     * Create an iterator for iterating over terminal pairing codes retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        ?string $profileId = null,
        ?string $sort = null,
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->send(
            (new GetPaginatedTerminalPairingCodesRequest($from, $limit, $profileId, $sort))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
