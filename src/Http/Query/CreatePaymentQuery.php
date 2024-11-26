<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Traits\ComposableFromArray;

class CreatePaymentQuery extends Query
{
    use ComposableFromArray;

    public ?string $include = null;

    public function __construct(
        ?string $include = null
    ) {
        $this->include = $include;
    }

    public function toArray(): array
    {
        return [
            'include' => $this->include,
        ];
    }
}
