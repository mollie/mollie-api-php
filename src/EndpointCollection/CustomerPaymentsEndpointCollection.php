<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\GetPaginatedCustomerPaymentsQueryFactory;
use Mollie\Api\Helpers;
use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class CustomerPaymentsEndpointCollection extends EndpointCollection
{
    /**
     * Create a subscription for a Customer
     *
     * @param  array  $payload
     *
     * @throws ApiException
     */
    public function createFor(Customer $customer, array $payload = []): Payment
    {
        return $this->createForId($customer->id, $payload, $testmode);
    }

    /**
     * Create a subscription for a Customer ID
     *
     * @param  string  $customerId
     * @param  array|  $payload
     *
     * @throws ApiException
     */
    public function createForId($customerId, array $payload = []): Payment
    {
        $testmode = Helpers::extractBool($payload, 'testmode', false);
        $profileId = Arr::get($payload, 'profileId');

        /** @var Payment */
        return $this->send((new CreateCustomerPaymentRequest($customerId, $profileId))->test($testmode));
    }

    /**
     * @param  string  $from  The first resource ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageFor(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): PaymentCollection
    {
        return $this->pageForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * @param  string  $from  The first resource ID you want to include in your list.
     *
     * @throws ApiException
     */
    public function pageForId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): PaymentCollection
    {
        $testmode = Helpers::extractBool($filters, 'testmode', false);
        $query = GetPaginatedCustomerPaymentsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(new GetPaginatedCustomerPaymentsRequest(
            $customerId,
            $query
        ))->test($testmode);
    }

    /**
     * Create an iterator for iterating over payments for the given customer, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
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
     * Create an iterator for iterating over payments for the given customer id, retrieved from Mollie.
     *
     * @param  string  $from  The first resource ID you want to include in your list.
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
        $query = GetPaginatedCustomerPaymentsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetPaginatedCustomerPaymentsRequest($customerId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
