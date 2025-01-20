<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreatePaymentCapturePayload;
use Mollie\Api\Http\Data\Metadata;

class CreatePaymentCapturePayloadFactory extends OldFactory
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
