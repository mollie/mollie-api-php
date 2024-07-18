<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;

class SubscriptionEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "customers_subscriptions";

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     *
     * @var string
     */
    protected static string $resourceIdPrefix = 'sub_';

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Subscription::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = SubscriptionCollection::class;

    /**
     * Create a subscription for a Customer
     *
     * @param Customer $customer
     * @param array $options
     * @param array $filters
     *
     * @return Subscription
     * @throws ApiException
     */
    public function createFor(Customer $customer, array $options = [], array $filters = []): Subscription
    {
        return $this->createForId($customer->id, $options, $filters);
    }

    /**
     * Create a subscription for a Customer
     *
     * @param string $customerId
     * @param array $options
     * @param array $filters
     *
     * @return Subscription
     * @throws ApiException
     */
    public function createForId(string $customerId, array $options = [], array $filters = []): Subscription
    {
        $this->parentId = $customerId;

        /** @var Subscription */
        return $this->createResource($options, $filters);
    }

    /**
     * Update a specific Subscription resource.
     *
     * Will throw an ApiException if the subscription id is invalid or the resource cannot be found.
     *
     * @param string $subscriptionId
     * @param string $customerId
     *
     * @param array $data
     *
     * @return null|Subscription
     * @throws ApiException
     */
    public function update(string $customerId, string $subscriptionId, array $data = []): ?Subscription
    {
        $this->guardAgainstInvalidId($subscriptionId);

        $this->parentId = $customerId;

        /** @var null|Subscription */
        return $this->updateResource($subscriptionId, $data);
    }

    /**
     * @param Customer $customer
     * @param string $subscriptionId
     * @param array $parameters
     *
     * @return Subscription
     * @throws ApiException
     */
    public function getFor(Customer $customer, string $subscriptionId, array $parameters = []): Subscription
    {
        return $this->getForId($customer->id, $subscriptionId, $parameters);
    }

    /**
     * @param string $customerId
     * @param string $subscriptionId
     * @param array $parameters
     *
     * @return Subscription
     * @throws ApiException
     */
    public function getForId(string $customerId, string $subscriptionId, array $parameters = []): Subscription
    {
        $this->parentId = $customerId;

        /** @var Subscription */
        return $this->readResource($subscriptionId, $parameters);
    }

    /**
     * @param Customer $customer
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return SubscriptionCollection
     * @throws ApiException
     */
    public function listFor(Customer $customer, ?string $from = null, ?int $limit = null, array $parameters = []): SubscriptionCollection
    {
        return $this->listForId($customer->id, $from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over subscriptions for the given customer, retrieved from Mollie.
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
     * @return SubscriptionCollection
     * @throws ApiException
     */
    public function listForId($customerId, ?string $from = null, ?int $limit = null, array $parameters = []): SubscriptionCollection
    {
        $this->parentId = $customerId;

        /** @var SubscriptionCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over subscriptions for the given customer id, retrieved from Mollie.
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
     * @param string $subscriptionId
     * @param array $data
     *
     * @return null|Subscription
     * @throws ApiException
     */
    public function cancelFor(Customer $customer, string $subscriptionId, array $data = []): ?Subscription
    {
        return $this->cancelForId($customer->id, $subscriptionId, $data);
    }

    /**
     * @param string $customerId
     * @param string $subscriptionId
     * @param array $data
     *
     * @return null|Subscription
     * @throws ApiException
     */
    public function cancelForId(string $customerId, string $subscriptionId, array $data = []): ?Subscription
    {
        $this->parentId = $customerId;

        /** @var null|Subscription */
        return $this->deleteResource($subscriptionId, $data);
    }

    /**
     * Retrieves a collection of Subscriptions from Mollie.
     *
     * @param string $from The first payment ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     *
     * @return SubscriptionCollection
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, array $parameters = []): SubscriptionCollection
    {
        $apiPath = 'subscriptions' . $this->buildQueryString(
            $this->getMergedFilters($parameters, $from, $limit)
        );

        $result = $this->client->performHttpCall(
            self::REST_LIST,
            $apiPath
        );

        /** @var SubscriptionCollection */
        return $this->buildResultCollection($result->decode());
    }

    /**
     * Create an iterator for iterating over subscriptions retrieved from Mollie.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iterator(?string $from = null, ?int $limit = null, array $parameters = [], bool $iterateBackwards = false): LazyCollection
    {
        $page = $this->page($from, $limit, $parameters);

        return $page->getAutoIterator($iterateBackwards);
    }
}
