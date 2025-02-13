<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedBalanceTransactionRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = BalanceTransactionCollection::class;

    private string $balanceId;

    public function __construct(
        string $balanceId,
        ?string $from = null,
        ?int $limit = null
    ) {
        $this->balanceId = $balanceId;

        parent::__construct($from, $limit);
    }

    public function resolveResourcePath(): string
    {
        return "balances/{$this->balanceId}/transactions";
    }
}
