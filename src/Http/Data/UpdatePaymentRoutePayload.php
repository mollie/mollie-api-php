<?php

namespace Mollie\Api\Http\Data;

use DateTimeInterface;

class UpdatePaymentRoutePayload extends Data
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
