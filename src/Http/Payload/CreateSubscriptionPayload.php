<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

class CreateSubscriptionPayload extends DataBag
{
    public ?string $status;

    public Money $amount;

    public ?int $times;

    public string $interval;

    public ?DateTimeInterface $startDate;

    public string $description;

    public ?string $method;

    public ?ApplicationFee $applicationFee;

    public ?Metadata $metadata;

    public ?string $webhookUrl;

    public ?string $mandateId;

    public ?string $profileId;

    public function __construct(
        Money $amount,
        string $interval,
        string $description,
        ?string $status = null,
        ?int $times = null,
        ?DateTimeInterface $startDate = null,
        ?string $method = null,
        ?ApplicationFee $applicationFee = null,
        ?Metadata $metadata = null,
        ?string $webhookUrl = null,
        ?string $mandateId = null,
        ?string $profileId = null
    ) {
        $this->amount = $amount;
        $this->interval = $interval;
        $this->description = $description;
        $this->status = $status;
        $this->times = $times;
        $this->startDate = $startDate;
        $this->method = $method;
        $this->applicationFee = $applicationFee;
        $this->metadata = $metadata;
        $this->webhookUrl = $webhookUrl;
        $this->mandateId = $mandateId;
        $this->profileId = $profileId;
    }

    public function data(): array
    {
        return [
            'amount' => $this->amount,
            'interval' => $this->interval,
            'description' => $this->description,
            'status' => $this->status,
            'times' => $this->times,
            'startDate' => $this->startDate ? $this->startDate->format('Y-m-d') : null,
            'method' => $this->method,
            'applicationFee' => $this->applicationFee,
            'metadata' => $this->metadata,
            'webhookUrl' => $this->webhookUrl,
            'mandateId' => $this->mandateId,
            'profileId' => $this->profileId,
        ];
    }
}
