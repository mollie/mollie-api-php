<?php

namespace Mollie\Api\Http\Data;

class UpdatePaymentLinkPayload extends Data
{
    public string $description;

    public bool $archived;

    public function __construct(
        string $description,
        bool $archived = false
    ) {
        $this->description = $description;
        $this->archived = $archived;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'archived' => $this->archived,
        ];
    }
}
