<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\UpdateProfilePayload;

class UpdateProfilePayloadFactory extends OldFactory
{
    public function create(): UpdateProfilePayload
    {
        return new UpdateProfilePayload(
            $this->get('name'),
            $this->get('website'),
            $this->get('email'),
            $this->get('phone'),
            $this->get('description'),
            $this->get('countriesOfActivity'),
            $this->get('businessCategory'),
            $this->get('mode')
        );
    }
}
