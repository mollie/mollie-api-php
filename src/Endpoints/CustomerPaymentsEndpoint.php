<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;

class CustomerPaymentsEndpoint extends CollectionEndpointAbstract
{
    protected string $resourcePath = "customers_payments";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Payment
    {
        return new Payment($this->client);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject(int $count, object $_links): PaymentCollection
    {
        return new PaymentCollection($this->client, $count, $_links);
    }

    /**
     * Create a subscription for a Customer
     *
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return Payment
     * @throws \Mollie\Api\Exceptions\ApiException
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
     * @return \Mollie\Api\Resources\Payment
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId($customerId, array $options = [], array $filters = [])
    {
        $this->parentId = $customerId;

        return parent::rest_create($options, $filters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return PaymentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
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
     * @return \Mollie\Api\Resources\PaymentCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $customerId, ?string $from = null, ?int $limit = null, array $parameters = []): PaymentCollection
    {
        $this->parentId = $customerId;

        return parent::rest_list($from, $limit, $parameters);
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

        return $this->rest_iterator($from, $limit, $parameters, $iterateBackwards);
    }
}
