<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

class ReleasePaymentAuthorizationRequest extends Request
{
    protected static string $method = Method::POST;

    private string $paymentId;

    public function __construct(
        string $paymentId
    ) {
        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        return 'payments/'.$this->paymentId.'/release-authorization';
    }
}
