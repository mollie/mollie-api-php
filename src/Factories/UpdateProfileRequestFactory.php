<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\UpdateProfileRequest;

class UpdateProfileRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): UpdateProfileRequest
    {
        return new UpdateProfileRequest(
            $this->id,
            $this->payload('name'),
            $this->payload('website'),
            $this->payload('email'),
            $this->payload('phone'),
            $this->payload('description'),
            $this->payload('countriesOfActivity'),
            $this->payload('businessCategory'),
            $this->payload('mode')
        );
    }
}
