<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Resources\LazyCollection;

class ClientEndpoint extends EndpointCollection
{
    protected string $resourcePath = "clients";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Client
    {
        return new Client($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): ClientCollection
    {
        return new ClientCollection($this->client, $count, $_links);
    }

    /**
     * Retrieve a client from Mollie.
     *
     * Will throw an ApiException if the client id is invalid or the resource cannot be found.
     * The client id corresponds to the organization id, for example "org_1337".
     *
     * @param string $clientId
     * @param array $parameters
     *
     * @return Client
     * @throws ApiException
     */
    public function get(string $clientId, array $parameters = []): Client
    {
        if (empty($clientId)) {
            throw new ApiException("Client ID is empty.");
        }

        return parent::readResource($clientId, $parameters);
    }

    /**
     * Retrieves a page of clients from Mollie.
     *
     * @param string $from The first client ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return ClientCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): ClientCollection
    {
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over clients retrieved from Mollie.
     *
     * @param string $from The first client ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
