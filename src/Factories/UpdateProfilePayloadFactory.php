<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\UpdateProfilePayload;

class UpdateProfilePayloadFactory extends Factory
{
    public function create(): UpdateProfilePayload
    {
        return new UpdateProfilePayload(
            name: $this->get('name'),
            website: $this->get('website'),
            email: $this->get('email'),
            phone: $this->get('phone'),
            description: $this->get('description'),
            countriesOfActivity: $this->get('countriesOfActivity'),
            businessCategory: $this->get('businessCategory'),
            mode: $this->get('mode')
        );
    }
}
