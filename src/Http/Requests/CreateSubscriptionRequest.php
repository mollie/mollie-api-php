<?php

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
    protected $hydratableResource = Subscription::class;

    private string $customerId;

    private Money $amount;

    private string $interval;

    private string $description;

    private ?string $status;

    private ?int $times;

    /**
     * @var Date|DateTimeInterface
     */
    private $startDate;

    private ?string $paymentMethod;

    private ?ApplicationFee $applicationFee;

    private ?array $metadata;

    private ?string $webhookUrl;

    private ?string $mandateId;

    private ?string $profileId;

    /**
     * @param Date|DateTimeInterface|null $startDate
     */
    public function __construct(
        string $customerId,
        Money $amount,
        string $interval,
        string $description,
        ?string $status = null,
        ?int $times = null,
        $startDate = null,
        ?string $method = null,
        ?ApplicationFee $applicationFee = null,
        ?array $metadata = null,
        ?string $webhookUrl = null,
        ?string $mandateId = null,
        ?string $profileId = null
    ) {
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->interval = $interval;
        $this->description = $description;
        $this->status = $status;
        $this->times = $times;
        $this->startDate = $startDate;
        $this->paymentMethod = $method;
        $this->applicationFee = $applicationFee;
        $this->metadata = $metadata;
        $this->webhookUrl = $webhookUrl;
        $this->mandateId = $mandateId;
        $this->profileId = $profileId;
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
