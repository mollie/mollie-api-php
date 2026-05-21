<?php

declare(strict_types=1);

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\RequestTerminalPairingCodeRequest;
use Mollie\Api\Types\TerminalPairingCodeQuery;

class RequestTerminalPairingCodeRequestFactory extends RequestFactory
{
    public function create(): RequestTerminalPairingCodeRequest
    {
        $includeQrCode = $this->queryIncludes('include', TerminalPairingCodeQuery::INCLUDE_QR_CODE);

        return new RequestTerminalPairingCodeRequest(
            $this->payload('profileId'),
            $this->query('includeQrCode', $includeQrCode),
        );
    }
}
