<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\SettlementCollection;
use Mollie\Api\Traits\IsIteratableRequest;

class GetPaginatedSettlementsRequest extends PaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = SettlementCollection::class;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $balanceId = null
    ) {
        parent::__construct($from, $limit);

        $this->query()
            ->add('balanceId', $balanceId);
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'settlements';
    }
}
