<?php

namespace Mollie\Api\Http\Payload;

class RefundRoute extends DataBag
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

    public function data(): array
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
