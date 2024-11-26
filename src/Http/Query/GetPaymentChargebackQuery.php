<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaymentChargebackQuery extends Query
{
    public array $include = [];

    public function __construct(
        array $include = []
    ) {
        $this->include = $include;
    }

    public function toArray(): array
    {
        return [
            'include' => Arr::join($this->include),
        ];
    }
}
