<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/terminals-get-pairing-code
 */
class GetTerminalPairingCodeRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected $hydratableResource = TerminalPairingCode::class;

    private string $id;

    private bool $includeQrCode;

    public function __construct(string $id, bool $includeQrCode = false)
    {
        $this->id = $id;
        $this->includeQrCode = $includeQrCode;
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includeQrCode ? 'details.qrCode' : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "terminals/pairing-codes/{$this->id}";
    }
}
