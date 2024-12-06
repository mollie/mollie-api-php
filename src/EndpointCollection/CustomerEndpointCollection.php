<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateCustomerPayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdateCustomerPayloadFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\CreateCustomerPayload;
use Mollie\Api\Http\Payload\UpdateCustomerPayload;
use Mollie\Api\Http\Requests\CreateCustomerRequest;
use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Resources\LazyCollection;

class CustomerEndpointCollection extends EndpointCollection
{
    /**
     * Creates a customer in Mollie.
     *
     * @param  array|CreateCustomerPayload  $data  An array containing details on the customer.
     * @param  array|bool|null  $testmode
     *
     * @throws ApiException
     */
    public function create($data = [], $testmode = []): Customer
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        if (! $data instanceof CreateCustomerPayload) {
            $data = CreateCustomerPayloadFactory::new($data)->create();
        }

        /** @var Customer */
        return $this->send((new CreateCustomerRequest($data))->test($testmode));
    }

    /**
     * Retrieve a single customer from Mollie.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     * @throws ApiException
     */
    public function get(string $id, $testmode = []): Customer
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        /** @var Customer */
        return $this->send((new GetCustomerRequest($id))->test($testmode));
    }

    /**
     * Update a specific Customer resource.
     *
     * Will throw an ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @throws ApiException
     */
    public function update(string $id, $data = []): ?Customer
    {
        if (! $data instanceof UpdateCustomerPayload) {
            $data = UpdateCustomerPayloadFactory::new($data)->create();
        }

        /** @var null|Customer */
        return $this->send(new UpdateCustomerRequest($id, $data));
    }

    /**
     * Deletes the given Customer.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function delete(string $id, $testmode = []): void
    {
        $testmode = Helpers::extractBool($testmode, 'testmode', false);

        $this->send((new DeleteCustomerRequest($id))->test($testmode));
    }

    /**
     * Retrieves a collection of Customers from Mollie.
     *
     * @param  string  $from  The first customer ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $filters = []): CustomerCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        /** @var CustomerCollection */
        return $this->send((new GetPaginatedCustomerRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over customers retrieved from Mollie.
     *
     * @param  string  $from  The first customer ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);

        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedCustomerRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
