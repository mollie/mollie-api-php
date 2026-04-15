<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

readonly class RefundRoute implements Resolvable
{
    public function __construct(
        public Money $amount,
        public string $organizationId,
    ) {}

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
