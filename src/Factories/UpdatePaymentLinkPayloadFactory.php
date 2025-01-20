<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\UpdatePaymentLinkPayload;

class UpdatePaymentLinkPayloadFactory extends OldFactory
{
    public function create(): UpdatePaymentLinkPayload
    {
        return new UpdatePaymentLinkPayload(
            $this->get('description'),
            $this->get('archived'),
        );
    }
}
