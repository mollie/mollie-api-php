<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class GetPaginatedSettlementChargebacksRequest extends GetPaginatedChargebacksRequest implements IsIteratable, SupportsTestmodeInQuery
{
    private string $settlementId;

    public function __construct(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        ?bool $includePayment = null,
        ?string $profileId = null
    ) {
        $this->settlementId = $settlementId;

        parent::__construct($from, $limit, $includePayment, $profileId);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "settlements/{$this->settlementId}/chargebacks";
    }
}
