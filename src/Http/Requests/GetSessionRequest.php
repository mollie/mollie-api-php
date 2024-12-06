<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Query\AnyQuery;
use Mollie\Api\Resources\Session;
use Mollie\Api\Types\Method;

class GetSessionRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Session::class;

    private string $sessionId;

    private AnyQuery $query;

    public function __construct(string $sessionId, AnyQuery $query)
    {
        $this->sessionId = $sessionId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "sessions/{$this->sessionId}";
    }
}
