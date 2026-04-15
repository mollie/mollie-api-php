<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class Address implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public ?string $title = null,
        public ?string $givenName = null,
        public ?string $familyName = null,
        public ?string $organizationName = null,
        public ?string $streetAndNumber = null,
        public ?string $streetAdditional = null,
        public ?string $postalCode = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $city = null,
        public ?string $region = null,
        public ?string $country = null,
    ) {}

    public function toArray(): array
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
