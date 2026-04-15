<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use DateTimeInterface;
use Mollie\Api\Contracts\Resolvable;

readonly class PaymentRoute implements Resolvable
{
    /**
     * @param  Date|DateTimeInterface|null  $delayUntil
     */
    public function __construct(
        public Money $amount,
        public string $organizationId,
        public Date|DateTimeInterface|null $delayUntil = null,
    ) {}

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'destination' => [
                'type' => 'organization',
                'organizationId' => $this->organizationId,
            ],
            'delayUntil' => $this->delayUntil,
        ];
    }
}
