<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetEnabledPaymentMethodsQuery;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Types\Method as HttpMethod;

class GetEnabledMethodsRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = HttpMethod::GET;

    public static string $targetResourceClass = MethodCollection::class;

    private ?GetEnabledPaymentMethodsQuery $query = null;

    public function __construct(?GetEnabledPaymentMethodsQuery $query = null)
    {
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    public function resolveResourcePath(): string
    {
        return 'methods';
    }
}
