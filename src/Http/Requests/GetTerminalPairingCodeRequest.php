<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\TerminalPairingCodeQuery;

/**
 * @see https://docs.mollie.com/reference/terminals-get-pairing-code
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\TerminalPairingCode>
 */
class GetTerminalPairingCodeRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = TerminalPairingCode::class;

    public function __construct(
        private string $id,
        private bool $includeQrCode = false,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includeQrCode ? TerminalPairingCodeQuery::INCLUDE_QR_CODE : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "terminals/pairing-codes/{$this->id}";
    }
}
