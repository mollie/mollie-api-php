<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Rules\Included;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Types\PaymentQuery;

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

    public function rules(): array
    {
        return [
            'include' => Included::in([PaymentQuery::INCLUDE_QR_CODE]),
        ];
    }
}
