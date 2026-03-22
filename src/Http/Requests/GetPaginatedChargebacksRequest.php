<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedChargebacksRequest extends PaginatedRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ChargebackCollection::class;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?bool $includePayment = null,
        ?string $profileId = null
    ) {
        parent::__construct($from, $limit);

        $this->query()
            ->add('embed', $includePayment ? PaymentIncludesQuery::PAYMENT : null)
            ->add('profileId', $profileId);
    }

    public function resolveResourcePath(): string
    {
        return 'chargebacks';
    }
}
