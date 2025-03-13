<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateCustomerRequestFactory;
use Mollie\Api\Factories\UpdateCustomerRequestFactory;
use Mollie\Api\Http\Requests\DeleteCustomerRequest;
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Utils\Utility;

class CustomerEndpointCollection extends EndpointCollection
{
    /**
     * Creates a customer in Mollie.
     *
     * @param  array|bool|null  $testmode
     *
     * @throws RequestException
     */
    public function create($data = [], $testmode = false): Customer
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        $request = CreateCustomerRequestFactory::new()
            ->withPayload($data)
            ->create();

        /** @var Customer */
        return $this->send($request->test($testmode));
    }

    /**
     * Retrieve a single customer from Mollie.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function get(string $id, $testmode = false): Customer
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var Customer */
        return $this->send((new GetCustomerRequest($id))->test($testmode));
    }

    /**
     * Update a specific Customer resource.
     *
     * Will throw an ApiException if the customer id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function update(string $id, array $data = []): ?Customer
    {
        $request = UpdateCustomerRequestFactory::new($id)
            ->withPayload($data)
            ->create();

        /** @var null|Customer */
        return $this->send($request);
    }

    /**
     * Deletes the given Customer.
     *
     * Will throw a ApiException if the customer id is invalid or the resource cannot be found.
     * Returns with HTTP status No Content (204) if successful.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function delete(string $id, $testmode = false): void
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        $this->send((new DeleteCustomerRequest($id))->test($testmode));
    }

    /**
     * Retrieves a collection of Customers from Mollie.
     *
     * @param  string  $from  The first customer ID you want to include in your list.
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function page(?string $from = null, ?int $limit = null, $testmode = false): CustomerCollection
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var CustomerCollection */
        return $this->send((new GetPaginatedCustomerRequest($from, $limit))->test($testmode));
    }

    /**
     * Create an iterator for iterating over customers retrieved from Mollie.
     *
     * @param  string  $from  The first customer ID you want to include in your list.
     * @param  bool|array  $testmode
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(?string $from = null, ?int $limit = null, $testmode = false, bool $iterateBackwards = false): LazyCollection
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        return $this->send(
            (new GetPaginatedCustomerRequest($from, $limit))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
