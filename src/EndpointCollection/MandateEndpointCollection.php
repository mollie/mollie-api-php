<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateMandatePayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Http\Data\CreateMandatePayload;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Utils\Utility;

class MandateEndpointCollection extends EndpointCollection
{
    /**
     * Creates a mandate for a specific customer.
     *
     * @param  array|CreateMandatePayload  $payload
     *
     * @throws ApiException
     */
    public function createForCustomer(Customer $customer, $payload = [], bool $testmode = false): Mandate
    {
        return $this->createForCustomerId($customer->id, $payload, $testmode);
    }

    /**
     * Creates a mandate for a specific customer ID.
     *
     * @param  array  $payload
     *
     * @throws ApiException
     */
    public function createForCustomerId(string $customerId, $payload = [], bool $testmode = false): Mandate
    {
        if (! $payload instanceof CreateMandatePayload) {
            $testmode = Utility::extractBool($payload, 'testmode', $testmode);
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
    public function getForCustomer(Customer $customer, string $mandateId, array $parameters = []): Mandate
    {
        return $this->getForCustomerId($customer->id, $mandateId, $parameters);
    }

    /**
     * Retrieve a specific mandate for a customer ID.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function getForCustomerId(string $customerId, string $mandateId, $testmode = false): Mandate
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Mandate */
        return $this->send((new GetMandateRequest($customerId, $mandateId))->test($testmode));
    }

    /**
     * Revoke a mandate for a specific customer.
     *
     *
     * @throws ApiException
     */
    public function revokeForCustomer(Customer $customer, string $mandateId, $data = []): void
    {
        $this->revokeForCustomerId($customer->id, $mandateId, $data);
    }

    /**
     * Revoke a mandate for a specific customer ID.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function revokeForCustomerId(string $customerId, string $mandateId, $testmode = false): void
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        $this->send((new RevokeMandateRequest($customerId, $mandateId))->test($testmode));
    }

    /**
     * Retrieves a collection of mandates for the given customer.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageForCustomer(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): MandateCollection
    {
        return $this->pageForCustomerId($customer->id, $from, $limit, $parameters);
    }

    /**
     * Retrieves a collection of mandates for the given customer ID.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageForCustomerId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): MandateCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);
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
    public function iteratorForCustomer(
        Customer $customer,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForCustomerId($customer->id, $from, $limit, $parameters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over mandates for the given customer ID.
     *
     * @param  string  $from  The first mandate ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iteratorForCustomerId(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);
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
