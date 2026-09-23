<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\ConnectBalanceTransferCollection;
use Mollie\Api\Traits\IsIteratableRequest;

/**
 * @extends SortablePaginatedRequest<\Mollie\Api\Resources\ConnectBalanceTransferCollection>
 */
class ListConnectBalanceTransfersRequest extends SortablePaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = ConnectBalanceTransferCollection::class;

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'connect/balance-transfers';
    }
}
