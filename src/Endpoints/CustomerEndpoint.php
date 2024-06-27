<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Resources\LazyCollection;

class CustomerEndpoint extends EndpointCollection
{
    protected string $resourcePath = "customers";

    protected static string $resourceIdPrefix = 'cst_';

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Customer
    {
        return new Customer($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): CustomerCollection
    {
        return new CustomerCollection($this->client, $count, $_links);
    }

    /**
     * Creates a customer in Mollie.
     *
     * @param array $data An array containing details on the customer.
     * @param array $filters
     *
     * @return Customer
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = []): Customer
    {
        /** @var Customer */
        return $this->createResource($data, $filters);
    }

    /**
     * Retrieve a single customer from Mollie.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @param string $customerId
     * @param array $parameters
     * @return Customer
     * @throws ApiException
     */
    public function get(string $customerId, array $parameters = []): Customer
    {
        /** @var Customer */
        return $this->readResource($customerId, $parameters);
    }

    /**
     * Update a specific Customer resource.
     *
     * Will throw an ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @param string $customerId
     * @param array $data
     * @return null|Customer
     * @throws ApiException
     */
    public function update(string $customerId, array $data = []): ?Customer
    {
        $this->guardAgainstInvalidId($customerId);

        /** @var null|Customer */
        return $this->updateResource($customerId, $data);
    }

    /**
     * Deletes the given Customer.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $customerId
     * @param array $data
     * @return null|Customer
     * @throws ApiException
     */
    public function delete(string $customerId, array $data = []): ?Customer
    {
        /** @var null|Customer */
        return $this->deleteResource($customerId, $data);
    }

    /**
     * Retrieves a collection of Customers from Mollie.
     *
     * @param string $from The first customer ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return CustomerCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): CustomerCollection
    {
        /** @var CustomerCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over customers retrieved from Mollie.
     *
     * @param string $from The first customer ID you want to include in your list.
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
