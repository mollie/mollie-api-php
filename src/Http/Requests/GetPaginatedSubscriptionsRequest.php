<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\PaginatedQuery;
use Mollie\Api\Resources\SubscriptionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSubscriptionsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = SubscriptionCollection::class;

    private string $customerId;

    public function __construct(string $customerId, ?PaginatedQuery $query = null)
    {
        $this->customerId = $customerId;

        parent::__construct($query);
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions";
    }
}
