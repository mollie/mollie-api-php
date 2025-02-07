<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateSubscriptionRequestFactory;
use Mollie\Api\Factories\GetAllPaginatedSubscriptionsRequestFactory;
use Mollie\Api\Factories\UpdateSubscriptionRequestFactory;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
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
     * @throws RequestException
     */
    public function getFor(Customer $customer, string $subscriptionId, $testmode = false): Subscription
    {
        return $this->getForId($customer->id, $subscriptionId, $testmode);
    }

    /**
     * Retrieve a single subscription from Mollie.
     *
     * @param  bool|array  $testmode
     *
     * @throws RequestException
     */
    public function getForId(string $customerId, string $subscriptionId, $testmode = false): Subscription
    {
        $testmode = Utility::extractBool($testmode, 'testmode');

        return $this->send((new GetSubscriptionRequest($customerId, $subscriptionId))->test($testmode));
    }

    /**
     * Creates a subscription for a Customer in Mollie.
     *
     * @throws RequestException
     */
    public function createFor(Customer $customer, array $payload = [], bool $testmode = false): Subscription
    {
        return $this->createForId($customer->id, $payload, $testmode);
    }

    /**
     * Creates a subscription for a Customer in Mollie.
     *
     * @throws RequestException
     */
    public function createForId(string $customerId, array $payload = [], bool $testmode = false): Subscription
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = CreateSubscriptionRequestFactory::new($customerId)
            ->withPayload($payload)
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * Update the given Subscription.
     *
     * @throws RequestException
     */
    public function update(string $customerId, string $subscriptionId, array $payload = [], bool $testmode = false): ?Subscription
    {
        $testmode = Utility::extractBool($payload, 'testmode', $testmode);

        $request = UpdateSubscriptionRequestFactory::new($customerId, $subscriptionId)
            ->withPayload($payload)
            ->create();

        return $this->send($request->test($testmode));
    }

    /**
     * Cancel the given Subscription.
     *
     * @throws RequestException
     */
    public function cancelFor(Customer $customer, string $subscriptionId, bool $testmode = false): Subscription
    {
        return $this->cancelForId($customer->id, $subscriptionId, $testmode);
    }

    /**
     * Cancel the given Subscription.
     *
     * @throws RequestException
     */
    public function cancelForId(string $customerId, string $subscriptionId, bool $testmode = false): Subscription
    {
        return $this->send((new CancelSubscriptionRequest($customerId, $subscriptionId))->test($testmode));
    }

    /**
     * Retrieve a page of subscriptions from Mollie.
     *
     * @throws RequestException
     */
    public function pageFor(Customer $customer, ?string $from = null, ?int $limit = null, array $filters = []): SubscriptionCollection
    {
        return $this->pageForId($customer->id, $from, $limit, $filters);
    }

    /**
     * Retrieve a page of subscriptions from Mollie.
     *
     * @throws RequestException
     */
    public function pageForId(string $customerId, ?string $from = null, ?int $limit = null, array $filters = []): SubscriptionCollection
    {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        return $this->send((new GetPaginatedSubscriptionsRequest($customerId, $from, $limit))->test($testmode));
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

        return $this->send(
            (new GetPaginatedSubscriptionsRequest($customerId, $from, $limit))
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

        $request = GetAllPaginatedSubscriptionsRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send($request->test($testmode));
    }

    public function iteratorForAll(
        ?string $from = null,
        ?int $limit = null,
        array $filters = [],
        bool $iterateBackwards = true
    ): LazyCollection {
        $testmode = Utility::extractBool($filters, 'testmode', false);

        $request = GetAllPaginatedSubscriptionsRequestFactory::new()
            ->withQuery([
                'from' => $from,
                'limit' => $limit,
                'filters' => $filters,
            ])
            ->create();

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
