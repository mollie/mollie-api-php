<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Traits\ComposableFromArray;

class Address implements DataProvider
{
    use ComposableFromArray;

    public ?string $title = null;

    public ?string $givenName = null;

    public ?string $familyName = null;

    public ?string $organizationName = null;

    public ?string $streetAndNumber = null;

    public ?string $streetAdditional = null;

    public ?string $postalCode = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $city = null;

    public ?string $region = null;

    public ?string $country = null;

    public function __construct(
        ?string $title = null,
        ?string $givenName = null,
        ?string $familyName = null,
        ?string $organizationName = null,
        ?string $streetAndNumber = null,
        ?string $streetAdditional = null,
        ?string $postalCode = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $city = null,
        ?string $region = null,
        ?string $country = null
    ) {
        $this->title = $title;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->organizationName = $organizationName;
        $this->streetAndNumber = $streetAndNumber;
        $this->streetAdditional = $streetAdditional;
        $this->postalCode = $postalCode;
        $this->email = $email;
        $this->phone = $phone;
        $this->city = $city;
        $this->region = $region;
        $this->country = $country;
    }

    public function data(): array
    {
        return [
            'title' => $this->title,
            'givenName' => $this->givenName,
            'familyName' => $this->familyName,
            'organizationName' => $this->organizationName,
            'streetAndNumber' => $this->streetAndNumber,
            'streetAdditional' => $this->streetAdditional,
            'postalCode' => $this->postalCode,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'region' => $this->region,
            'country' => $this->country,
        ];
    }
}
