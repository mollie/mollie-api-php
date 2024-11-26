<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateProfilePayload;

class CreateProfilePayloadFactory extends Factory
{
    public function create(): CreateProfilePayload
    {
        return new CreateProfilePayload(
            name: $this->get('name'),
            website: $this->get('website'),
            email: $this->get('email'),
            phone: $this->get('phone'),
            description: $this->get('description'),
            countriesOfActivity: $this->get('countriesOfActivity'),
            businessCategory: $this->get('businessCategory')
        );
    }
}
