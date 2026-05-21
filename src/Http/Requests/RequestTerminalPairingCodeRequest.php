<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\TerminalPairingCodeQuery;

/**
 * @see https://docs.mollie.com/reference/terminals-request-pairing-code
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\TerminalPairingCode>
 */
class RequestTerminalPairingCodeRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = TerminalPairingCode::class;

    public function __construct(
        private string $profileId,
        private bool $includeQrCode = false,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'profileId' => $this->profileId,
        ];
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includeQrCode ? TerminalPairingCodeQuery::INCLUDE_QR_CODE : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'terminals/pairing-codes';
    }
}
