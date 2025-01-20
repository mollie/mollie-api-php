<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreateClientLinkPayload;
use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Http\Data\OwnerAddress;

class CreateClientLinkPayloadFactory extends OldFactory
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
