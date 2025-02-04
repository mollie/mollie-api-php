<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedSubscriptionPaymentsRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $customerId;

    private string $subscriptionId;

    private ?string $from;

    private ?string $limit;

    public function __construct(string $customerId, string $subscriptionId, ?string $from = null, ?string $limit = null)
    {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;
        $this->from = $from;
        $this->limit = $limit;
    }

    public function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}/payments";
    }
}
