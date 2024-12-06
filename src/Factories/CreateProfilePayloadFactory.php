<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateProfilePayload;

class CreateProfilePayloadFactory extends Factory
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
