<?php

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload
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

    private ?DateTimeInterface $expiresAt;

    private ?array $allowedMethods;

    public function __construct(
        string $description,
        ?Money $amount = null,
        ?string $redirectUrl = null,
        ?string $webhookUrl = null,
        ?string $profileId = null,
        ?bool $reusable = null,
        ?DateTimeInterface $expiresAt = null,
        ?array $allowedMethods = null
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->redirectUrl = $redirectUrl;
        $this->webhookUrl = $webhookUrl;
        $this->profileId = $profileId;
        $this->reusable = $reusable;
        $this->expiresAt = $expiresAt;
        $this->allowedMethods = $allowedMethods;
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
