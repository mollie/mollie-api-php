<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Resources\Session;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    protected $hydratableResource = Session::class;

    private string $sessionId;

    private AnyData $payload;

    public function __construct(string $sessionId, AnyData $payload)
    {
        $this->sessionId = $sessionId;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "sessions/{$this->sessionId}";
    }
}
