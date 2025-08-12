<?php

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\DateTime;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentLink::class;

    private string $description;

    private ?Money $amount;

    private ?string $redirectUrl;

    private ?string $webhookUrl;

    private ?string $profileId;

    private ?bool $reusable;

    /**
     * @var DateTime|DateTimeInterface
     */
    private $expiresAt;

    private ?array $allowedMethods;

    private ?string $sequenceType;

    private ?string $customerId;

    public function __construct(
        string $description,
        ?Money $amount = null,
        ?string $redirectUrl = null,
        ?string $webhookUrl = null,
        ?string $profileId = null,
        ?bool $reusable = null,
        $expiresAt = null,
        ?array $allowedMethods = null,
        ?string $sequenceType = null,
        ?string $customerId = null
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->redirectUrl = $redirectUrl;
        $this->webhookUrl = $webhookUrl;
        $this->profileId = $profileId;
        $this->reusable = $reusable;
        $this->expiresAt = $expiresAt;
        $this->allowedMethods = $allowedMethods;
        $this->sequenceType = $sequenceType;
        $this->customerId = $customerId;
    }

    protected function defaultPayload(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'redirectUrl' => $this->redirectUrl,
            'webhookUrl' => $this->webhookUrl,
            'profileId' => $this->profileId,
            'reusable' => $this->reusable,
            'expiresAt' => $this->expiresAt,
            'allowedMethods' => $this->allowedMethods,
            'sequenceType' => $this->sequenceType,
            'customerId' => $this->customerId,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payment-links';
    }
}
