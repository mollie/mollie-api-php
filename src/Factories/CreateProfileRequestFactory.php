<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreateProfileRequest;

class CreateProfileRequestFactory extends RequestFactory
{
    public function create(): CreateProfileRequest
    {
        return new CreateProfileRequest(
            $this->payload('name'),
            $this->payload('website'),
            $this->payload('email'),
            $this->payload('phone'),
            $this->payload('description'),
            $this->payload('countriesOfActivity'),
            $this->payload('businessCategory')
        );
    }
}
