<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\ApplicationFee;
use Mollie\Api\Http\Data\Date;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/subscriptions-api/create-subscription
 */
class CreateSubscriptionRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Subscription::class;

    private ?string $paymentMethod;

    public function __construct(
        private string $customerId,
        private Money $amount,
        private string $interval,
        private string $description,
        private ?string $status = null,
        private ?int $times = null,
        private Date|DateTimeInterface|null $startDate = null,
        ?string $method = null,
        private ?ApplicationFee $applicationFee = null,
        private ?array $metadata = null,
        private ?string $webhookUrl = null,
        private ?string $mandateId = null,
        private ?string $profileId = null,
    ) {
        $this->paymentMethod = $method;
    }

    protected function defaultPayload(): array
    {
        return [
            'amount' => $this->amount,
            'interval' => $this->interval,
            'description' => $this->description,
            'status' => $this->status,
            'times' => $this->times,
            'startDate' => $this->startDate,
            'method' => $this->paymentMethod,
            'applicationFee' => $this->applicationFee,
            'metadata' => $this->metadata,
            'webhookUrl' => $this->webhookUrl,
            'mandateId' => $this->mandateId,
            'profileId' => $this->profileId,
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
