<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\Method;

class GetSubscriptionRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Subscription::class;

    private string $customerId;

    private string $id;

    public function __construct(string $customerId, string $id)
    {
        $this->customerId = $customerId;
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->id}";
    }
}
