<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

/**
 * @extends PaginatedRequest<\Mollie\Api\Resources\PaymentCollection>
 */
class GetPaginatedSubscriptionPaymentsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = PaymentCollection::class;

    private string $customerId;

    private string $subscriptionId;

    public function __construct(string $customerId, string $subscriptionId, ?string $from = null, ?int $limit = null)
    {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;

        parent::__construct($from, $limit);
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}/payments";
    }
}
