<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedCustomerPaymentsRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = PaymentCollection::class;

    private string $customerId;

    private ?string $from;

    private ?int $limit;

    private ?string $sort;

    private ?string $profileId;

    public function __construct(
        string $customerId,
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        ?string $profileId = null
    ) {
        $this->customerId = $customerId;
        $this->from = $from;
        $this->limit = $limit;
        $this->sort = $sort;
        $this->profileId = $profileId;
    }

    public function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
