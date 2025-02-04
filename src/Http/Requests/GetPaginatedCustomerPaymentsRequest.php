<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedCustomerPaymentsRequest extends SortablePaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $customerId;

    public function __construct(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        ?string $profileId = null
    ) {
        $this->customerId = $customerId;

        parent::__construct($from, $limit, $sort);

        $this->query()
            ->add('profileId', $profileId);
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
