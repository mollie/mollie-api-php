<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Date;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/subscriptions-api/update-subscription
 */
class UpdateSubscriptionRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Subscription::class;

    /**
     * @param Date|DateTimeInterface|null $startDate
     */
    public function __construct(
        private string $customerId,
        private string $subscriptionId,
        private ?Money $amount = null,
        private ?string $description = null,
        private ?string $interval = null,
        private $startDate = null,
        private ?int $times = null,
        private ?array $metadata = null,
        private ?string $webhookUrl = null,
        private ?string $mandateId = null,
    ) {
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
