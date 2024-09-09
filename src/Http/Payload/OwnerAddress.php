<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Traits\ComposableFromArray;

class OwnerAddress implements DataProvider
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

    public function data(): array
    {
        return [
            'streetAndNumber' => $this->streetAndNumber,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'region' => $this->region,
            'country' => $this->country,
        ];
    }
}
