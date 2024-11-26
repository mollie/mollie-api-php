<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\UpdatePaymentLinkPayload;

class UpdatePaymentLinkPayloadFactory extends Factory
{
    public function create(): UpdatePaymentLinkPayload
    {
        return new UpdatePaymentLinkPayload(
            $this->get('description'),
            $this->get('archived'),
        );
    }
}
