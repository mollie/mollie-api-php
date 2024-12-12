<?php

namespace Mollie\Api\Http\Data;

class RefundRoute extends Data
{
    public Money $amount;

    public string $organizationId;

    public function __construct(
        Money $amount,
        string $organizationId
    ) {
        $this->amount = $amount;
        $this->organizationId = $organizationId;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'source' => [
                'type' => 'organization',
                'organizationId' => $this->organizationId,
            ],
        ];
    }
}