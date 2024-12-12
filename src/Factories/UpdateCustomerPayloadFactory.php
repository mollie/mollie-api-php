<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Data\UpdateCustomerPayload;

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
