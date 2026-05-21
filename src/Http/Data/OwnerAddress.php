<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class OwnerAddress implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $country,
        public ?string $streetAndNumber = null,
        public ?string $postalCode = null,
        public ?string $city = null,
        public ?string $region = null,
    ) {
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
