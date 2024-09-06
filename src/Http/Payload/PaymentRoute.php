<?php

namespace Mollie\Api\Http\Payload;

use DateTime;

class PaymentRoute extends DataBag
{
    public function __construct(
        public readonly Money $amount,
        public readonly string $organizationId,
        public readonly ?DateTime $delayUntil = null,
    ) {}

    public function data(): array
    {
        return [
            'amount' => $this->amount,
            'destination' => [
                'type' => 'organization',
                'organizationId' => $this->organizationId,
            ],
            'delayUntil' => $this->delayUntil?->format('Y-m-d'),
        ];
    }
}
