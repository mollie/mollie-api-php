<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

class UpdateSubscriptionPayload extends DataBag
{
    public ?Money $amount;

    public ?string $description;

    public ?string $interval;

    public ?DateTimeInterface $startDate;

    public ?int $times;

    public ?Metadata $metadata;

    public ?string $webhookUrl;

    public ?string $mandateId;

    public function __construct(
        ?Money $amount = null,
        ?string $description = null,
        ?string $interval = null,
        ?DateTimeInterface $startDate = null,
        ?int $times = null,
        ?Metadata $metadata = null,
        ?string $webhookUrl = null,
        ?string $mandateId = null
    ) {
        $this->amount = $amount;
        $this->description = $description;
        $this->interval = $interval;
        $this->startDate = $startDate;
        $this->times = $times;
        $this->metadata = $metadata;
        $this->webhookUrl = $webhookUrl;
        $this->mandateId = $mandateId;
    }

    public function data(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'interval' => $this->interval,
            'startDate' => $this->startDate?->format('Y-m-d'),
            'times' => $this->times,
            'metadata' => $this->metadata,
            'webhookUrl' => $this->webhookUrl,
            'mandateId' => $this->mandateId,
        ];
    }
}
