<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\Payload\Money;

class GetAllMethodsQuery extends Query
{
    private string $locale;

    private array $include;

    private ?Money $amount;

    public function __construct(
        string $locale,
        array $include = [],
        ?Money $amount = null,
    ) {
        $this->locale = $locale;
        $this->include = $include;
        $this->amount = $amount;
    }

    public function toArray(): array
    {
        return [
            'include' => Arr::join($this->include),
            'locale' => $this->locale,
            'amount' => $this->amount?->data(),
        ];
    }
}
