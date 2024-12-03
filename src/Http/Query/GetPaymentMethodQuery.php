<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\MethodQuery;

class GetPaymentMethodQuery extends Query
{
    private ?string $locale;

    private ?string $currency;

    private ?string $profileId;

    private bool $includeIssuers;

    private bool $includePricing;

    public function __construct(
        ?string $locale = null,
        ?string $currency = null,
        ?string $profileId = null,
        bool $includeIssuers = false,
        bool $includePricing = false
    ) {
        $this->locale = $locale;
        $this->currency = $currency;
        $this->profileId = $profileId;
        $this->includeIssuers = $includeIssuers;
        $this->includePricing = $includePricing;
    }

    public function toArray(): array
    {
        return [
            'locale' => $this->locale,
            'currency' => $this->currency,
            'profileId' => $this->profileId,
            'include' => Arr::join([
                $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
                $this->includePricing ? MethodQuery::INCLUDE_PRICING : null,
            ]),
        ];
    }
}
