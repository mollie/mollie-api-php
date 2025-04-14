<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\GetClientRequestFactory;
use Mollie\Api\Factories\GetPaginatedClientRequestFactory;
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
     *
     * @throws RequestException
     */
    public function get(string $id, array $query = []): Client
    {
        $request = GetClientRequestFactory::new($id)
            ->withQuery($query)
            ->create();

        /** @var Client */
        return $this->send($request);
    }

    /**
     * Retrieves a page of clients from Mollie.
     *
     * @param  string  $from  The first client ID you want to include in your list.
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): ClientCollection
    {
        $request = GetPaginatedClientRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        /** @var ClientCollection */
        return $this->send($request);
    }

    /**
     * Create an iterator for iterating over clients retrieved from Mollie.
     *
     * @param  string  $from  The first client ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $request = GetPaginatedClientRequestFactory::new()
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
        );
    }
}
