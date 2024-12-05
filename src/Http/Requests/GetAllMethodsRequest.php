<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\GetAllMethodsQuery;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Types\Method as HttpMethod;

class GetAllMethodsRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = HttpMethod::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = MethodCollection::class;

    private GetAllMethodsQuery $query;

    public function __construct(?GetAllMethodsQuery $query = null)
    {
        $this->query = $query ?: new GetAllMethodsQuery();
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function resolveResourcePath(): string
    {
        return 'methods/all';
    }
}
