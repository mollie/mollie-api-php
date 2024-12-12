<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Data\GetClientQuery;
use Mollie\Api\Resources\Client;
use Mollie\Api\Types\Method;

class GetClientRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Client::class;

    private string $id;

    private ?GetClientQuery $query;

    public function __construct(string $id, ?GetClientQuery $query = null)
    {
        $this->id = $id;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    public function resolveResourcePath(): string
    {
        return "clients/{$this->id}";
    }
}
