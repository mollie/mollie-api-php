<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class OwnerAddress implements Arrayable
{
    use ComposableFromArray;

    public ?string $streetAndNumber = null;

    public ?string $postalCode = null;

    public ?string $city = null;

    public ?string $region = null;

    public string $country;

    public function __construct(
        string $country,
        ?string $streetAndNumber = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $region = null
    ) {
        $this->country = $country;
        $this->streetAndNumber = $streetAndNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->region = $region;
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'streetAndNumber' => $this->streetAndNumber,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'region' => $this->region,
        ];
    }
}
