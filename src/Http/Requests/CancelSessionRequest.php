<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Session;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Session>
 */
class CancelSessionRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::DELETE;

    protected ?string $hydratableResource = Session::class;

    public function __construct(
        private string $sessionId,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "sessions/{$this->sessionId}";
    }
}
