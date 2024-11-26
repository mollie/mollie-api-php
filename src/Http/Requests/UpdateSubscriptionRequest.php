<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Payload\UpdateSubscriptionPayload;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateSubscriptionRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInQuery
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Subscription::class;

    private string $customerId;

    private string $subscriptionId;

    private UpdateSubscriptionPayload $payload;

    public function __construct(
        string $customerId,
        string $subscriptionId,
        UpdateSubscriptionPayload $payload
    ) {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}";
    }
}
