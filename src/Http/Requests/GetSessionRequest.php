<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Session;
use Mollie\Api\Types\Method;

class GetSessionRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected $hydratableResource = Session::class;

    private string $sessionId;

    public function __construct(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function resolveResourcePath(): string
    {
        return "sessions/{$this->sessionId}";
    }
}
