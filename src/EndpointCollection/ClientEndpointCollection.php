<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetClientQueryFactory;
use Mollie\Api\Factories\GetPaginatedClientQueryFactory;
use Mollie\Api\Http\Query\GetClientQuery;
use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\ClientCollection;
use Mollie\Api\Resources\LazyCollection;

class ClientEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a client from Mollie.
     *
     * Will throw an ApiException if the client id is invalid or the resource cannot be found.
     * The client id corresponds to the organization id, for example "org_1337".
     *
     * @param  string  $id  The client ID.
     * @param  GetClientQuery|array  $query  The query parameters.
     *
     * @throws ApiException
     */
    public function get(string $id, $query = []): Client
    {
        if (! $query instanceof GetClientQuery) {
            $query = GetClientQueryFactory::new($query)->create();
        }

        /** @var Client */
        return $this->send(new GetClientRequest($id, $query));
    }

    /**
     * Retrieves a page of clients from Mollie.
     *
     * @param  string  $from  The first client ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): ClientCollection
    {
        $query = GetPaginatedClientQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var ClientCollection */
        return $this->send(new GetPaginatedClientRequest($query));
    }

    /**
     * Create an iterator for iterating over clients retrieved from Mollie.
     *
     * @param  string  $from  The first client ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $query = GetPaginatedClientQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedClientRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
