<?php

namespace Mollie\Api\Http\Payload;

class UpdatePaymentLinkPayload extends DataBag
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

    public function data(): array
    {
        return [
            'description' => $this->description,
            'archived' => $this->archived,
        ];
    }
}
