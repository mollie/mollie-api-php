<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateClientLinkPayload;
use Mollie\Api\Http\Payload\Owner;
use Mollie\Api\Http\Payload\OwnerAddress;

class CreateClientLinkPayloadFactory extends Factory
{
    public function create(): CreateClientLinkPayload
    {
        return new CreateClientLinkPayload(
            Owner::fromArray($this->get('owner')),
            $this->get('name'),
            OwnerAddress::fromArray($this->get('address')),
            $this->get('registrationNumber'),
            $this->get('vatNumber')
        );
    }
}
