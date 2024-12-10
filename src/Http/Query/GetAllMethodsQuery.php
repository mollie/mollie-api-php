<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Types\MethodQuery;

class GetAllMethodsQuery implements Arrayable
{
    private ?string $locale;

    private bool $includeIssuers;

    private bool $includePricing;

    private ?Money $amount;

    public function __construct(
        bool $includeIssuers = false,
        bool $includePricing = false,
        ?string $locale = null,
        ?Money $amount = null
    ) {
        $this->locale = $locale;
        $this->includeIssuers = $includeIssuers;
        $this->includePricing = $includePricing;
        $this->amount = $amount;
    }

    public function toArray(): array
    {
        return [
            'include' => array_filter([
                $this->includeIssuers ? MethodQuery::INCLUDE_ISSUERS : null,
                $this->includePricing ? MethodQuery::INCLUDE_PRICING : null,
            ]),
            'locale' => $this->locale,
            'amount' => $this->amount,
        ];
    }
}
