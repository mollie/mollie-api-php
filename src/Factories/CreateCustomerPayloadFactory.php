<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreateCustomerPayload;
use Mollie\Api\Http\Data\Metadata;

class CreateCustomerPayloadFactory extends OldFactory
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
