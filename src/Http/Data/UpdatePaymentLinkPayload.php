<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

class UpdatePaymentLinkPayload implements Resolvable
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
