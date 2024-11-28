<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedMandateRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = MandateCollection::class;

    private string $customerId;

    public function __construct(string $customerId, PaginatedQuery $query)
    {
        parent::__construct($query);

        $this->customerId = $customerId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates";
    }
}