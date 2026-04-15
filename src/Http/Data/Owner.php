<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class Owner implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $email,
        public string $givenName,
        public string $familyName,
        public ?string $locale = null,
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'givenName' => $this->givenName,
            'familyName' => $this->familyName,
            'locale' => $this->locale,
        ];
    }
}
