<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateClientLink;
use Mollie\Api\Http\Payload\Owner;
use Mollie\Api\Http\Payload\OwnerAddress;

class CreateClientLinkPayloadFactory extends Factory
{
    public function create(): CreateClientLink
    {
        return new CreateClientLink(
            Owner::fromArray($this->get('owner')),
            $this->get('name'),
            OwnerAddress::fromArray($this->get('address')),
            $this->get('registrationNumber'),
            $this->get('vatNumber')
        );
    }
}
