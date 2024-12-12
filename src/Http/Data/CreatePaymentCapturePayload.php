<?php

namespace Mollie\Api\Http\Data;

class CreatePaymentCapturePayload extends Data
{
    public string $description;

    public ?Money $amount;

    public ?Metadata $metadata;

    public function __construct(
        string $description,
        ?Money $amount = null,
        ?Metadata $metadata = null
    ) {
        $this->amount = $amount;
        $this->description = $description;
        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'metadata' => $this->metadata,
        ];
    }
}
