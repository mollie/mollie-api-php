<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Http\Data\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;

class CreateClientLinkRequestFactory extends RequestFactory
{
    public function create(): CreateClientLinkRequest
    {
        return new CreateClientLinkRequest(
            Owner::fromArray($this->payload('owner')),
            $this->payload('name'),
            OwnerAddress::fromArray($this->payload('address')),
            $this->payload('registrationNumber'),
            $this->payload('vatNumber')
        );
    }
}
