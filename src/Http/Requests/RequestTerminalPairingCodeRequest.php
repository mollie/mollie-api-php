<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/terminals-request-pairing-code
 */
class RequestTerminalPairingCodeRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected $hydratableResource = TerminalPairingCode::class;

    private string $profileId;

    private bool $includeQrCode;

    public function __construct(string $profileId, bool $includeQrCode = false)
    {
        $this->profileId = $profileId;
        $this->includeQrCode = $includeQrCode;
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includeQrCode ? 'details.qrCode' : null,
        ];
    }

    protected function defaultPayload(): array
    {
        return [
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'terminals/pairing-codes';
    }
}
