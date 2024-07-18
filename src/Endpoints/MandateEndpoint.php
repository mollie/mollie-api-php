<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;

class MandateEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "customers_mandates";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Mandate::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = MandateCollection::class;

    /**
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createFor(Customer $customer, array $options = [], array $filters = []): Mandate
    {
        return $this->createForId($customer->id, $options, $filters);
    }

    /**
     * @param string $customerId
     * @param array $options
     * @param array $filters
     *
     * @return Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createForId(string $customerId, array $options = [], array $filters = []): Mandate
    {
        $this->parentId = $customerId;

        /** @var Mandate */
        return $this->createResource($options, $filters);
    }

    /**
     * @param Customer $customer
     * @param string $mandateId
     * @param array $parameters
     *
     * @return Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Customer $customer, $mandateId, array $parameters = []): Mandate
    {
        return $this->getForId($customer->id, $mandateId, $parameters);
    }

    /**
     * @param string $customerId
     * @param string $mandateId
     * @param array $parameters
     *
     * @return Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $customerId, $mandateId, array $parameters = [])
    {
        $this->parentId = $customerId;

        /** @var Mandate */
        return $this->readResource($mandateId, $parameters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return MandateCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listFor(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): MandateCollection
    {
        return $this->listForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over mandates for the given customer, retrieved from Mollie.
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
     * @param ?string $from
     * @param ?int $limit
     * @param array $parameters
     *
     * @return MandateCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function listForId(string $customerId, ?string $from = null, ?int $limit = null, array $parameters = []): MandateCollection
    {
        $this->parentId = $customerId;

        /** @var MandateCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over mandates for the given customer id, retrieved from Mollie.
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

    /**
     * @param Customer $customer
     * @param string $mandateId
     * @param array $data
     *
     * @return null|Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function revokeFor(Customer $customer, string $mandateId, array $data = []): ?Mandate
    {
        return $this->revokeForId($customer->id, $mandateId, $data);
    }

    /**
     * @param string $customerId
     * @param string $mandateId
     * @param array $data
     *
     * @return null|Mandate
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function revokeForId(string $customerId, string $mandateId, array $data = []): ?Mandate
    {
        $this->parentId = $customerId;

        /** @var null|Mandate */
        return $this->deleteResource($mandateId, $data);
    }
}
