<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;

class Recipient implements Arrayable
{
    public string $type;

    public ?string $title;

    public ?string $givenName;

    public ?string $familyName;

    public ?string $organizationName;

    public ?string $organizationNumber;

    public ?string $vatNumber;

    public string $email;

    public ?string $phone;

    public string $streetAndNumber;

    public ?string $streetAdditional;

    public string $postalCode;

    public string $city;

    public ?string $region;

    public string $country;

    public string $locale;

    public function __construct(
        string $type,
        string $email,
        string $streetAndNumber,
        string $postalCode,
        string $city,
        string $country,
        string $locale,
        ?string $title = null,
        ?string $givenName = null,
        ?string $familyName = null,
        ?string $organizationName = null,
        ?string $organizationNumber = null,
        ?string $vatNumber = null,
        ?string $phone = null,
        ?string $streetAdditional = null,
        ?string $region = null
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->organizationName = $organizationName;
        $this->organizationNumber = $organizationNumber;
        $this->vatNumber = $vatNumber;
        $this->email = $email;
        $this->phone = $phone;
        $this->streetAndNumber = $streetAndNumber;
        $this->streetAdditional = $streetAdditional;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->region = $region;
        $this->country = $country;
        $this->locale = $locale;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'givenName' => $this->givenName,
            'familyName' => $this->familyName,
            'organizationName' => $this->organizationName,
            'organizationNumber' => $this->organizationNumber,
            'vatNumber' => $this->vatNumber,
            'email' => $this->email,
            'phone' => $this->phone,
            'streetAndNumber' => $this->streetAndNumber,
            'streetAdditional' => $this->streetAdditional,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'region' => $this->region,
            'country' => $this->country,
            'locale' => $this->locale,
        ];
    }
}
