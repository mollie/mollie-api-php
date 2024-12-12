<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreatePaymentLinkPayload;

class CreatePaymentLinkPayloadFactory extends Factory
{
    public function create(): CreatePaymentLinkPayload
    {
        return new CreatePaymentLinkPayload(
            $this->get('description'),
            $this->mapIfNotNull('amount', fn(array $amount) => MoneyFactory::new($amount)->create()),
            $this->get('redirectUrl'),
            $this->get('webhookUrl'),
            $this->get('profileId'),
            $this->get('reusable'),
            $this->get('expiresAt'),
        );
    }
}
