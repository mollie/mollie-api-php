<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\UpdatePaymentRoutePayload;

class UpdatePaymentRoutePayloadFactory extends OldFactory
{
    public function create(): UpdatePaymentRoutePayload
    {
        return new UpdatePaymentRoutePayload(
            DateTimeImmutable::createFromFormat('Y-m-d', $this->get('releaseDate')),
        );
    }
}
