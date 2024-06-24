<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;

class TerminalEndpoint extends EndpointCollection
{
    protected string $resourcePath = "terminals";

    public const RESOURCE_ID_PREFIX = 'term_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Terminal
    {
        return new Terminal($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): TerminalCollection
    {
        return new TerminalCollection($this->client, $count, $_links);
    }

    /**
     * Retrieve terminal from Mollie.
     *
     * Will throw a ApiException if the terminal id is invalid or the resource cannot be found.
     *
     * @param string $terminalId
     * @param array $parameters
     *
     * @return Terminal
     * @throws ApiException
     */
    public function get(string $terminalId, array $parameters = []): Terminal
    {
        if (empty($terminalId) || strpos($terminalId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid terminal ID: '{$terminalId}'. A terminal ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::readResource($terminalId, $parameters);
    }

    /**
     * Retrieves a collection of Terminals from Mollie for the current organization / profile, ordered from newest to oldest.
     *
     * @param string $from The first terminal ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return TerminalCollection
     * @throws ApiException
     */
    public function collect(?string $from = null, ?int $limit = null, array $parameters = []): TerminalCollection
    {
        return parent::fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over terminals retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
