<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreateProfilePayload;

class CreateProfilePayloadFactory extends OldFactory
{
    public function create(): CreateProfilePayload
    {
        return new CreateProfilePayload(
            $this->get('name'),
            $this->get('website'),
            $this->get('email'),
            $this->get('phone'),
            $this->get('description'),
            $this->get('countriesOfActivity'),
            $this->get('businessCategory')
        );
    }
}
