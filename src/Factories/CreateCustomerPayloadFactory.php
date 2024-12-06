<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateCustomerPayload;
use Mollie\Api\Http\Payload\Metadata;

class CreateCustomerPayloadFactory extends Factory
{
    public function create(): CreateCustomerPayload
    {
        return new CreateCustomerPayload(
            $this->get('name'),
            $this->get('email'),
            $this->get('locale'),
            $this->mapIfNotNull('metadata', Metadata::class),
        );
    }
}
