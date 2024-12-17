<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\PaginatedQuery;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedMandateRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = MandateCollection::class;

    private string $customerId;

    public function __construct(string $customerId, ?PaginatedQuery $query = null)
    {
        parent::__construct($query);

        $this->customerId = $customerId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates";
    }
}
