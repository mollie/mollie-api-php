<?php

namespace Mollie\Api\Http\Data;

use DateTimeInterface;
use Mollie\Api\Contracts\Resolvable;

class UpdatePaymentRoutePayload implements Resolvable
{
    private DateTimeInterface $releaseDate;

    public function __construct(
        DateTimeInterface $releaseDate
    ) {
        $this->releaseDate = $releaseDate;
    }

    public function toArray(): array
    {
        return [
            'releaseDate' => $this->releaseDate,
        ];
    }
}
