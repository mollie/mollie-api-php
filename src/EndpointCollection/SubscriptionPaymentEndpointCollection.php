<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionPaymentsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Utils\Utility;

class SubscriptionPaymentEndpointCollection extends EndpointCollection
{
    /**
     * Retrieves a paginated collection of Subscription Payments from Mollie.
     *
     * @param  string|null  $from  The first payment ID you want to include in your list.
     * @param  int|null  $limit  The maximum amount of results you want to retrieve per page.
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function pageFor(Subscription $subscription, ?string $from = null, ?int $limit = null, $testmode = false): PaymentCollection
    {
        return $this->pageForIds($subscription->customerId, $subscription->id, $from, $limit, $testmode);
    }

    /**
     * Retrieves a paginated collection of Subscription Payments from Mollie.
     *
     * @param  string|null  $from  The first payment ID you want to include in your list.
     * @param  int|null  $limit  The maximum amount of results you want to retrieve per page.
     * @param  bool|array  $testmode
     *
     * @throws ApiException
     */
    public function pageForIds(
        string $customerId,
        string $subscriptionId,
        ?string $from = null,
        ?int $limit = null,
        $testmode = false
    ): PaymentCollection {
        $testmode = Utility::extractBool($testmode, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send((new GetPaginatedSubscriptionPaymentsRequest($customerId, $subscriptionId, $query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over payments for the given subscription, retrieved from Mollie.
     *
     * @param  bool|array  $testmode
     */
    public function iteratorFor(
        Subscription $subscription,
        ?string $from = null,
        ?int $limit = null,
        $testmode = false,
        bool $iterateBackwards = false
    ): LazyCollection {
        return $this->iteratorForIds($subscription->customerId, $subscription->id, $from, $limit, $testmode, $iterateBackwards);
    }

    /**
     * Create an iterator for iterating over payments for the given subscription ID, retrieved from Mollie.
     *
     * @param  bool|array  $testmode
     */
    public function iteratorForIds(
        string $customerId,
        string $subscriptionId,
        ?string $from = null,
        ?int $limit = null,
        $testmode = false,
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($testmode, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(
            (new GetPaginatedSubscriptionPaymentsRequest($customerId, $subscriptionId, $query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}
