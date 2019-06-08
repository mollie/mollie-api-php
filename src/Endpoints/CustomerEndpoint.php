<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;

class CustomerEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "customers";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Customer
     */
    protected function getResourceObject()
    {
        return new Customer($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return CustomerCollection
     */
    protected function getResourceCollectionObject($count, $_links)
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
    public function create(array $data = [], array $filters = [])
    {
        return $this->rest_create($data, $filters);
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
    public function get($customerId, array $parameters = [])
    {
        return $this->rest_read($customerId, $parameters);
    }

    /**
     * Deletes the given Customer.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param string $customerId
     *
     * @param array $data
     * @return null
     * @throws ApiException
     */
    public function delete($customerId, array $data = [])
    {
        return $this->rest_delete($customerId, $data);
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
    public function page($from = null, $limit = null, array $parameters = [])
    {
        return $this->rest_list($from, $limit, $parameters);
    }
}
