<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Payload\CreatePaymentPayload;
use Mollie\Api\Http\Query\CreatePaymentQuery;

class CreateCustomerPaymentRequest extends CreatePaymentRequest implements HasPayload, SupportsTestmodeInPayload
{
    protected string $customerId;

    public function __construct(
        string $customerId,
        CreatePaymentPayload $payload,
        ?CreatePaymentQuery $query = null,
    ) {
        parent::__construct($payload, $query);

        $this->customerId = $customerId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
