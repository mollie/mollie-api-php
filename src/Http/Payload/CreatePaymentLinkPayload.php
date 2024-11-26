<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

class CreatePaymentLinkPayload extends DataBag
{
    public string $description;

    public ?Money $amount;

    public ?string $redirectUrl;

    public ?string $webhookUrl;

    public ?string $profileId;

    public ?bool $reusable;

    public ?DateTimeInterface $expiresAt;

    public function __construct(
        string $description,
        ?Money $amount = null,
        ?string $redirectUrl = null,
        ?string $webhookUrl = null,
        ?string $profileId = null,
        ?bool $reusable = null,
        ?DateTimeInterface $expiresAt = null
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->redirectUrl = $redirectUrl;
        $this->webhookUrl = $webhookUrl;
        $this->profileId = $profileId;
        $this->reusable = $reusable;
        $this->expiresAt = $expiresAt;
    }

    public function data(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'redirectUrl' => $this->redirectUrl,
            'webhookUrl' => $this->webhookUrl,
            'profileId' => $this->profileId,
            'reusable' => $this->reusable,
            'expiresAt' => $this->expiresAt?->format('Y-m-d'),
        ];
    }
}
