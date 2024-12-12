<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Utils\Utility;
use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Data\UpdatePaymentPayload;

class UpdatePaymentPayloadFactory extends Factory
{
    public function create(): UpdatePaymentPayload
    {
        return new UpdatePaymentPayload(
            $this->get('description'),
            $this->get('redirectUrl'),
            $this->get('cancelUrl'),
            $this->get('webhookUrl'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('method'),
            $this->get('locale'),
            $this->get('restrictPaymentMethodsToCountry'),
            $this->get('additional') ?? Utility::filterByProperties(UpdatePaymentPayload::class, $this->data),
        );
    }
}
