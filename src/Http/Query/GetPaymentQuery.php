<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Rules\Included;
use Mollie\Api\Types\PaymentQuery;

class GetPaymentQuery extends Query
{
    public array $embed = [];

    public array $include = [];

    public ?bool $testmode = null;

    public function __construct(
        array $embed = [],
        array $include = [],
        ?bool $testmode = null
    ) {
        $this->embed = $embed;
        $this->include = $include;
        $this->testmode = $testmode;
    }

    public function toArray(): array
    {
        return [
            'embed' => Arr::join($this->embed),
            'include' => Arr::join($this->include),
            'testmode' => $this->testmode,
        ];
    }

    public function rules(): array
    {
        return [
            'embed' => Included::in(PaymentQuery::EMBEDS),
            'include' => Included::in(PaymentQuery::INCLUDES),
        ];
    }
}
