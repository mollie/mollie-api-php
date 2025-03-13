<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedRefundsRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = RefundCollection::class;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        bool $embedPayment = false,
        ?string $profileId = null
    ) {
        parent::__construct($from, $limit);

        $this->query()
            ->add('embed', $embedPayment ? PaymentIncludesQuery::PAYMENT : null)
            ->add('profileId', $profileId);
    }

    public function resolveResourcePath(): string
    {
        return 'refunds';
    }
}
