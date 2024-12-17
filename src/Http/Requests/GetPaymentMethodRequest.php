<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\GetPaymentMethodQuery;
use Mollie\Api\Resources\Method;
use Mollie\Api\Types\Method as HttpMethod;

class GetPaymentMethodRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    protected $hydratableResource = Method::class;

    private ?GetPaymentMethodQuery $query = null;

    private string $methodId;

    public function __construct(string $methodId, ?GetPaymentMethodQuery $query = null)
    {
        $this->methodId = $methodId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    public function resolveResourcePath(): string
    {
        return "methods/{$this->methodId}";
    }
}
