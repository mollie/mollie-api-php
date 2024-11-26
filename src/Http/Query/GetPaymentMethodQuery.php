<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaymentMethodQuery extends Query
{
    public function __construct(
        private ?string $locale = null,
        private ?string $currency = null,
        private ?string $profileId = null,
        private ?array $include = null,
    ) {}

    public function toArray(): array
    {
        return [
            'locale' => $this->locale,
            'currency' => $this->currency,
            'profileId' => $this->profileId,
            'include' => $this->include ? Arr::join($this->include) : null,
        ];
    }
}
