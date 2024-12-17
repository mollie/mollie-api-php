<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaginatedCustomerPaymentsQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedCustomerPaymentsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $customerId;

    public function __construct(
        string $customerId,
        ?GetPaginatedCustomerPaymentsQuery $query = null
    ) {
        parent::__construct($query);

        $this->customerId = $customerId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
