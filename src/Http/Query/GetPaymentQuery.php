<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaymentQuery extends Query
{
    public array $embed = [];

    public array $include = [];

    public function __construct(
        array $embed = [],
        array $include = [],
    ) {
        $this->embed = $embed;
        $this->include = $include;
    }

    public function toArray(): array
    {
        return [
            'embed' => Arr::join($this->embed),
            'include' => Arr::join($this->include),
        ];
    }
}
