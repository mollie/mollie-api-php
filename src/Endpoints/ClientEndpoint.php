<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Resources\LazyCollection;

class ClientEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "clients";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Client::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = ClientCollection::class;

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

        /** @var Client */
        return $this->readResource($clientId, $parameters);
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
        /** @var ClientCollection */
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
