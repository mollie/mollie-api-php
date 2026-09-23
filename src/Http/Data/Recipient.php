<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;

readonly class Recipient implements Arrayable
{
    public function __construct(
        public string $type,
        public string $email,
        public string $streetAndNumber,
        public string $postalCode,
        public string $city,
        public string $country,
        public string $locale,
        public ?string $title = null,
        public ?string $givenName = null,
        public ?string $familyName = null,
        public ?string $organizationName = null,
        public ?string $organizationNumber = null,
        public ?string $vatNumber = null,
        public ?string $phone = null,
        public ?string $streetAdditional = null,
        public ?string $region = null,
    ) {
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
