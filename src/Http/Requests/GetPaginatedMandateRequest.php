<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
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

    public function __construct(string $customerId, ?string $from = null, ?int $limit = null)
    {
        $this->customerId = $customerId;

        parent::__construct($from, $limit);
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates";
    }
}
