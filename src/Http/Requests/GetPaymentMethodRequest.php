<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaymentMethodQuery;
use Mollie\Api\Resources\Method;
use Mollie\Api\Types\Method as HttpMethod;

class GetPaymentMethodRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    public static string $targetResourceClass = Method::class;

    private GetPaymentMethodQuery $query;

    private string $methodId;

    public function __construct(string $methodId, GetPaymentMethodQuery $query)
    {
        $this->methodId = $methodId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "methods/{$this->methodId}";
    }
}
