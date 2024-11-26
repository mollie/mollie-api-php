<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSubscriptionPaymentsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PaymentCollection::class;

    private string $customerId;

    private string $subscriptionId;

    public function __construct(string $customerId, string $subscriptionId, PaginatedQuery $query)
    {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;

        parent::__construct($query);
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}/payments";
    }
}
