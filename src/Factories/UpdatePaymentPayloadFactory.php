<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\Metadata;
use Mollie\Api\Http\Payload\UpdatePayment;

class UpdatePaymentPayloadFactory extends Factory
{
    public function create(): UpdatePayment
    {
        return new UpdatePayment(
            $this->get('description'),
            $this->get('redirectUrl'),
            $this->get('cancelUrl'),
            $this->get('webhookUrl'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('method'),
            $this->get('locale'),
            $this->get('restrictPaymentMethodsToCountry'),
            $this->get('additional') ?? Helpers::filterByProperties(UpdatePayment::class, $this->data),
            $this->get('testmode')
        );
    }
}
