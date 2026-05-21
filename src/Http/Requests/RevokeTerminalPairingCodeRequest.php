<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/terminals-revoke-pairing-code
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\TerminalPairingCode>
 */
class RevokeTerminalPairingCodeRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::DELETE;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = TerminalPairingCode::class;

    public function __construct(
        private string $id,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "terminals/pairing-codes/{$this->id}";
    }
}
