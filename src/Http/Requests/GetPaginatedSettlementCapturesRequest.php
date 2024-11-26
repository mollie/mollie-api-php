<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaginatedSettlementCapturesQuery;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSettlementCapturesRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = CaptureCollection::class;

    private string $settlementId;

    private ?GetPaginatedSettlementCapturesQuery $query;

    public function __construct(string $settlementId, ?GetPaginatedSettlementCapturesQuery $query = null)
    {
        $this->settlementId = $settlementId;

        parent::__construct($query);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "settlements/{$this->settlementId}/captures";
    }
}
