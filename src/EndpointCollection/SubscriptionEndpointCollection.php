<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateSubscriptionPayloadFactory;
use Mollie\Api\Factories\GetAllPaginatedSubscriptionsQueryFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdateSubscriptionPayloadFactory;
use Mollie\Api\Http\Data\CreateSubscriptionPayload;
use Mollie\Api\Http\Data\UpdateSubscriptionPayload;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use Mollie\Api\Utils\Utility;

class SubscriptionEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a single subscription from Mollie.
     *
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function getFor(Customer $customer, string $subscriptionId, $testmode = []): Subscription
    {
        return $this->getForId($customer->id, $subscriptionId, $testmode);
    }

    /**
     * Retrieve a single subscription from Mollie.
     *
     * @throws ApiException
     */
    public function getForId(string $customerId, string $subscriptionId, $testmode = []): Subscription
    {
        $testmode = Utility::extractBool($testmode, 'testmode');

        return $this->send((new GetSubscriptionRequest($customerId, $subscriptionId))->test($testmode));
    }

    /**
     * Creates a subscription for a Customer in Mollie.
     *
     * @param  array|CreateSubscriptionPayload  $data  An array containing details on the subscription.
     *
     * @throws ApiException
     */
    public function createFor(Customer $customer, $data = [], bool $testmode = false): Subscription
    {
        return $this->createForId($customer->id, $data, $testmode);
    }

    /**
     * Creates a subscription for a Customer in Mollie.
     *
     * @param  array|CreateSubscriptionPayload  $data  An array containing details on the subscription.
     *
     * @throws ApiException
     */
    public function createForId(string $customerId, $data = [], bool $testmode = false): Subscription
    {
        if (! $data instanceof CreateSubscriptionPayload) {
            $testmode = Utility::extractBool($data, 'testmode', $testmode);
            $data = CreateSubscriptionPayloadFactory::new($data)->create();
        }

        return $this->send((new CreateSubscriptionRequest($customerId, $data))->test($testmode));
    }

    /**
     * Update the given Subscription.
     *
     * @param  array|UpdateSubscriptionPayload  $data
     *
     * @throws ApiException
     */
    public function update(string $customerId, string $subscriptionId, $data = [], bool $testmode = false): ?Subscription
    {
        if (! $data instanceof UpdateSubscriptionPayload) {
            $testmode = Utility::extractBool($data, 'testmode', $testmode);
            $data = UpdateSubscriptionPayloadFactory::new($data)->create();
        }

        return $this->send((new UpdateSubscriptionRequest($customerId, $subscriptionId, $data))->test($testmode));
    }

    /**
     * Cancel the given Subscription.
     *
     * @throws ApiException
     */
    public function cancelFor(Customer $customer, string $subscriptionId, bool $testmode = false): ?Subscription
    {
        return $this->cancelForId($customer->id, $subscriptionId, $testmode);
    }

    /**
     * Cancel the given Subscription.
     *
     * @throws ApiException
     */
    public function cancelForId(string $customerId, string $subscriptionId, bool $testmode = false): ?Subscription
    {
        return $this->send((new CancelSubscriptionRequest($customerId, $subscriptionId))->test($testmode));
    }

    /**
     * Retrieve a page of subscriptions from Mollie.
     *
     * @throws ApiException
     */
    public function pageFor(Customer $customer, ?string $from = null, ?int $limit = null, array $filters = []): SubscriptionCollection
    {
        return $this->pageForId($customer->id, $from, $limit, $filters);
    }

    /**
     * Retrieve a page of subscriptions from Mollie.
     *
     * @throws ApiException
     */
    public function pageForId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): SubscriptionCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send((new GetPaginatedSubscriptionsRequest($customerId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over subscriptions for the given customer, retrieved from Mollie.
     */
    public function iteratorFor(
        Customer $customer,
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForId($customer->id, $from, $limit, $filters, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over subscriptions for the given customer id, retrieved from Mollie.
     */
    public function iteratorForId(
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
        ])->create();

        return $this->send(
            (new GetPaginatedSubscriptionsRequest($customerId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }

    public function allFor(?string $from = null, ?int $limit = null, array $filters = []): SubscriptionCollection
    {
        return $this->allForId($from, $limit, $filters);
    }

    public function allForId(
        ?string $from = null,
        ?int $limit = null,
        array $filters = []
    ): SubscriptionCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $query = GetAllPaginatedSubscriptionsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send((new GetAllPaginatedSubscriptionsRequest($query))->test($testmode));
    }

    public function iteratorForAll(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = true
    ): LazyCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);
        $query = GetAllPaginatedSubscriptionsQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
            'filters' => $filters,
        ])->create();

        return $this->send(
            (new GetAllPaginatedSubscriptionsRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
