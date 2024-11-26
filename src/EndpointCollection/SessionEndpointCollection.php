<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateSessionPayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdateSessionPayloadFactory;
use Mollie\Api\Http\Payload\CreateSessionPayload;
use Mollie\Api\Http\Payload\UpdateSessionPayload;
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
     * @throws ApiException
     */
    public function get(string $sessionId, array $parameters = []): Session
    {
        /** @var Session */
        return $this->send(new GetSessionRequest($sessionId, $parameters));
    }

    /**
     * Creates a session in Mollie.
     *
     * @param  CreateSessionPayload|array  $data  An array containing details on the session.
     *
     * @throws ApiException
     */
    public function create($data = [], array $filters = []): Session
    {
        if (! $data instanceof CreateSessionPayload) {
            $data = CreateSessionPayloadFactory::new($data)
                ->create();
        }

        /** @var Session */
        return $this->send(new CreateSessionRequest($data, $filters));
    }

    /**
     * Update the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @param  array|UpdateSessionPayload  $data
     *
     * @throws ApiException
     */
    public function update(string $id, $data = []): Session
    {
        if (! $data instanceof UpdateSessionPayload) {
            $data = UpdateSessionPayloadFactory::new($data)
                ->create();
        }

        /** @var Session */
        return $this->send(new UpdateSessionRequest($id, $data));
    }

    /**
     * Cancel the given Session.
     *
     * Will throw a ApiException if the session id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function cancel(string $id, array $parameters = []): ?Session
    {
        /** @var Session|null */
        return $this->send(new CancelSessionRequest($id, $parameters));
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
        $query = PaginatedQueryFactory::new([
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
        $query = PaginatedQueryFactory::new([
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
