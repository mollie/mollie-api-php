<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateSessionRequestFactory;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use Mollie\Api\Http\Requests\CancelSessionRequest;
use Mollie\Api\Http\Requests\DynamicPaginatedRequest;
use Mollie\Api\Http\Requests\DynamicPutRequest;
use Mollie\Api\Http\Requests\GetSessionRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Session;
use Mollie\Api\Resources\SessionCollection;

class SessionEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single session from Mollie.
     *
     * @throws RequestException
     */
    public function get(string $sessionId): Session
    {
        /** @var Session */
        return $this->send(new GetSessionRequest($sessionId));
    }

    /**
     * Creates a session in Mollie.
     *
     * @throws RequestException
     */
    public function create(array $payload = []): Session
    {
        $request = CreateSessionRequestFactory::new()
            ->withPayload($payload)
            ->create();

        /** @var Session */
        return $this->send($request);
    }

    /**
     * Update the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     *
     * @throws RequestException
     */
    public function update(string $id, array $payload = [], array $query = []): Session
    {
        $request = new DynamicPutRequest("sessions/{$id}", $payload, $query);

        $request->setHydratableResource(Session::class);

        /** @var Session */
        return $this->send($request);
    }

    /**
     * Cancel the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @throws RequestException
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
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): SessionCollection
    {
        $query = SortablePaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        $request = new DynamicPaginatedRequest('sessions', array_merge($filters, $query->toArray()));

        $request->setHydratableResource(SessionCollection::class);

        /** @var SessionCollection */
        return $this->send($request);
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

        $request = new DynamicPaginatedRequest('sessions', array_merge($filters, $query->toArray()));

        $request->setHydratableResource(SessionCollection::class);

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
        );
    }
}
