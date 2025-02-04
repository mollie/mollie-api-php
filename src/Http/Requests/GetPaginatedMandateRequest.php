<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

class GetPaginatedMandateRequest extends ResourceHydratableRequest implements IsIteratable, SupportsTestmodeInQuery
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = MandateCollection::class;

    private string $customerId;

    private ?string $from;

    private ?int $limit;

    public function __construct(string $customerId, ?string $from = null, ?int $limit = null)
    {
        $this->customerId = $customerId;
        $this->from = $from;
        $this->limit = $limit;
    }

    public function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates";
    }
}
