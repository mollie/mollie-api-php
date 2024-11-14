<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Rules\Included;
use Mollie\Api\Types\PaymentRefundQuery;

class GetPaymentRefundQuery extends Query
{
    public array $include = [];

    public ?bool $testmode = null;

    public function __construct(
        array $include = [],
        ?bool $testmode = null
    ) {
        $this->include = $include;
        $this->testmode = $testmode;
    }

    public function toArray(): array
    {
        return [
            'include' => Arr::join($this->include),
            'testmode' => $this->testmode,
        ];
    }

    public function rules(): array
    {
        return [
            'include' => Included::in(PaymentRefundQuery::INCLUDES),
        ];
    }
}
