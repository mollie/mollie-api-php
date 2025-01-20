<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\RequestApplePayPaymentSessionPayload;

class ApplePayPaymentSessionPayloadFactory extends OldFactory
{
    public function create(): RequestApplePayPaymentSessionPayload
    {
        return new RequestApplePayPaymentSessionPayload(
            $this->get('domain'),
            $this->get('validationUrl'),
            $this->get('profileId'),
        );
    }
}
