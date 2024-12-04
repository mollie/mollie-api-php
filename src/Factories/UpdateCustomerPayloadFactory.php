<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\Metadata;
use Mollie\Api\Http\Payload\UpdateCustomerPayload;

class UpdateCustomerPayloadFactory extends Factory
{
    public function create(): UpdateCustomerPayload
    {
        return new UpdateCustomerPayload(
            $this->get('name'),
            $this->get('email'),
            $this->get('locale'),
            $this->mapIfNotNull('metadata', Metadata::class),
        );
    }
}
