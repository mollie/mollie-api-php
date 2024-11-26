<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateMandatePayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\CreateMandatePayload;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;

class MandateEndpointCollection extends EndpointCollection
{
    /**
     * Creates a mandate for a specific customer.
     *
     * @param  array  $payload
     *
     * @throws ApiException
     */
    public function createFor(Customer $customer, $payload = [], bool $testmode = false): Mandate
    {
        return $this->createForId($customer->id, $payload, $testmode);
    }

    /**
     * Creates a mandate for a specific customer ID.
     *
     * @param  array  $payload
     *
     * @throws ApiException
     */
    public function createForId(string $customerId, $payload = [], bool $testmode = false): Mandate
    {
        if (! $payload instanceof CreateMandatePayload) {
            $testmode = Helpers::extractBool($payload, 'testmode', $testmode);
            $payload = CreateMandatePayloadFactory::new($payload)->create();
        }

        /** @var Mandate */
        return $this->send((new CreateMandateRequest($customerId, $payload))->test($testmode));
    }

    /**
     * Retrieve a specific mandate for a customer.
     *
     *
     * @throws ApiException
     */
    public function getFor(Customer $customer, string $mandateId, array $parameters = []): Mandate
    {
        return $this->getForId($customer->id, $mandateId, $parameters);
    }

    /**
     * Retrieve a specific mandate for a customer ID.
     *
     * @param  array  $testmode
     *
     * @throws ApiException
     */
    public function getForId(string $customerId, string $mandateId, $testmode = []): Mandate
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Mandate */
        return $this->send((new GetMandateRequest($customerId, $mandateId))->test($testmode));
    }

    /**
     * Revoke a mandate for a specific customer.
     *
     *
     * @throws ApiException
     */
    public function revokeFor(Customer $customer, string $mandateId, $data = []): void
    {
        $this->revokeForId($customer->id, $mandateId, $data);
    }

    /**
     * Revoke a mandate for a specific customer ID.
     *
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function revokeForId(string $customerId, string $mandateId, $testmode = []): void
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        $this->send((new RevokeMandateRequest($customerId, $mandateId))->test($testmode));
    }

    /**
     * Retrieves a collection of mandates for the given customer.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageFor(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): MandateCollection
    {
        return $this->pageForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * Retrieves a collection of mandates for the given customer ID.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageForId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): MandateCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var MandateCollection */
        return $this->send((new GetPaginatedMandateRequest($customerId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over mandates for the given customer.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorFor(
        Customer $customer,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($customer->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over mandates for the given customer ID.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForId(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedMandateRequest($customerId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
