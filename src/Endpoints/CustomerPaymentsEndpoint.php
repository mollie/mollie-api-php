<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class CustomerPaymentsEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "customers_payments";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Payment::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = PaymentCollection::class;

    /**
     * Create a subscription for a Customer
     *
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return Payment
     * @throws ApiException
     */
    public function createFor(Customer $customer, array $options = [], array $filters = []): Payment
    {
        return $this->createForId($customer->id, $options, $filters);
    }

    /**
     * Create a subscription for a Customer ID
     *
     * @param string $customerId
     * @param array $options
     * @param array $filters
     *
     * @return Payment
     * @throws ApiException
     */
    public function createForId($customerId, array $options = [], array $filters = [])
    {
        $this->parentId = $customerId;

        /** @var Payment */
        return $this->createResource($options, $filters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function listFor(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): PaymentCollection
    {
        return $this->listForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over payments for the given customer, retrieved from Mollie.
     *
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
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
     * @param string $customerId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function listForId(string $customerId, ?string $from = null, ?int $limit = null, array $parameters = []): PaymentCollection
    {
        $this->parentId = $customerId;

        /** @var PaymentCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over payments for the given customer id, retrieved from Mollie.
     *
     * @param string $customerId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $customerId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}
