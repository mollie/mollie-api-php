<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Http\Payload\AnyPayload;
use Mollie\Api\Http\Query\AnyQuery;
use Mollie\Api\Http\Requests\CancelSessionRequest;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Http\Requests\GetPaginatedSessionsRequest;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Http\Requests\UpdateSessionRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;

class SessionEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single session from Mollie.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @param  array|AnyQuery  $query
     *
     * @throws ApiException
     */
    public function get(string $sessionId, $query = []): Session
    {
        if (! $query instanceof AnyQuery) {
            $query = AnyQuery::fromArray($query);
        }

        /** @var Session */
        return $this->send(new GetSessionRequest($sessionId, $query));
    }

    /**
     * Creates a session in Mollie.
     *
     * @param  array|AnyPayload  $payload
     * @param  array|AnyQuery  $query
     *
     * @throws ApiException
     */
    public function create($payload = [], $query = []): Session
    {
        if (! $payload instanceof AnyPayload) {
            $payload = AnyPayload::fromArray($payload);
        }

        if (! $query instanceof AnyQuery) {
            $query = AnyQuery::fromArray($query);
        }

        /** @var Session */
        return $this->send(new CreateSessionRequest($payload, $query));
    }

    /**
     * Update the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @param  array|AnyPayload  $payload
     *
     * @throws ApiException
     */
    public function update(string $id, $payload = []): Session
    {
        if (! $payload instanceof AnyPayload) {
            $payload = AnyPayload::fromArray($payload);
        }

        /** @var Session */
        return $this->send(new UpdateSessionRequest($id, $payload));
    }

    /**
     * Cancel the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function cancel(string $id): void
    {
        $this->send(new CancelSessionRequest($id));
    }

    /**
     * Get the sessions endpoint.
     *
     * @param  string|null  $from  The first session ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): SessionCollection
    {
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var SessionCollection */
        return $this->send(new GetPaginatedSessionsRequest($query));
    }

    /**
     * Create an iterator for iterating over sessions retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedSessionsRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
