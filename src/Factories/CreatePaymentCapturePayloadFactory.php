<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreatePaymentCapturePayload;
use Mollie\Api\Http\Payload\Metadata;

class CreatePaymentCapturePayloadFactory extends Factory
{
    public function create(): CreatePaymentCapturePayload
    {
        return new CreatePaymentCapturePayload(
            $this->get('description'),
            $this->mapIfNotNull('amount', fn (array $item) => MoneyFactory::new($item)->create()),
            $this->mapIfNotNull('metadata', Metadata::class)
        );
    }
}
