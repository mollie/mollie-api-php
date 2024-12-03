<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

class UpdatePaymentRoutePayload extends DataBag
{
    private DateTimeInterface $releaseDate;

    public function __construct(
        DateTimeInterface $releaseDate
    ) {
        $this->releaseDate = $releaseDate;
    }

    public function data(): array
    {
        return [
            'releaseDate' => $this->releaseDate->format('Y-m-d'),
        ];
    }
}
