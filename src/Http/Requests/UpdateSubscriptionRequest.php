<?php

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\Money;
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
    protected $hydratableResource = Subscription::class;

    private string $customerId;

    private string $subscriptionId;

    private ?Money $amount;

    private ?string $description;

    private ?string $interval;

    private ?DateTimeInterface $startDate;

    private ?int $times;

    private ?array $metadata;

    private ?string $webhookUrl;

    private ?string $mandateId;

    public function __construct(
        string $customerId,
        string $subscriptionId,
        ?Money $amount = null,
        ?string $description = null,
        ?string $interval = null,
        ?DateTimeInterface $startDate = null,
        ?int $times = null,
        ?array $metadata = null,
        ?string $webhookUrl = null,
        ?string $mandateId = null
    ) {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;
        $this->amount = $amount;
        $this->description = $description;
        $this->interval = $interval;
        $this->startDate = $startDate;
        $this->times = $times;
        $this->metadata = $metadata;
        $this->webhookUrl = $webhookUrl;
        $this->mandateId = $mandateId;
    }

    protected function defaultPayload(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'interval' => $this->interval,
            'startDate' => $this->startDate,
            'times' => $this->times,
            'metadata' => $this->metadata,
            'webhookUrl' => $this->webhookUrl,
            'mandateId' => $this->mandateId,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}";
    }
}
