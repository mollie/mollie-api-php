<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\SubscriptionCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedSubscriptionsRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = SubscriptionCollection::class;

    private string $customerId;

    private ?string $limit;

    private ?string $from;

    public function __construct(string $customerId, ?string $limit = null, ?string $from = null)
    {
        $this->customerId = $customerId;
        $this->limit = $limit;
        $this->from = $from;
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => $this->limit,
            'from' => $this->from,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions";
    }
}
