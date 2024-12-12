<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaginatedSettlementChargebacksQuery;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSettlementChargebacksRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = ChargebackCollection::class;

    private string $settlementId;

    public function __construct(string $settlementId, ?GetPaginatedSettlementChargebacksQuery $query = null)
    {
        $this->settlementId = $settlementId;

        parent::__construct($query);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "settlements/{$this->settlementId}/chargebacks";
    }
}