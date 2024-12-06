<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Http\Query\PaginatedQuery;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedBalanceTransactionRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = BalanceTransactionCollection::class;

    private string $balanceId;

    public function __construct(
        string $balanceId,
        ?PaginatedQuery $query = null
    ) {
        parent::__construct($query);

        $this->balanceId = $balanceId;
    }

    public function resolveResourcePath(): string
    {
        return "balances/{$this->balanceId}/transactions";
    }
}
