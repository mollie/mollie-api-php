<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Types\TerminalPairingCodeQuery;

class GetTerminalPairingCodeRequestFactory extends RequestFactory
{
    public function __construct(
        private string $id,
    ) {
    }

    public function create(): GetTerminalPairingCodeRequest
    {
        $includeQrCode = $this->queryIncludes('include', TerminalPairingCodeQuery::INCLUDE_QR_CODE);

        return new GetTerminalPairingCodeRequest(
            $this->id,
            $this->query('includeQrCode', $includeQrCode),
        );
    }
}
