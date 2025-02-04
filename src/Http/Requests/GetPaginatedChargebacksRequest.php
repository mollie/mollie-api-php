<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedChargebacksRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ChargebackCollection::class;

    private ?string $from;

    private ?int $limit;

    private ?bool $includePayment;

    private ?string $profileId;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?bool $includePayment = null,
        ?string $profileId = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->includePayment = $includePayment;
        $this->profileId = $profileId;
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'chargebacks';
    }
}
