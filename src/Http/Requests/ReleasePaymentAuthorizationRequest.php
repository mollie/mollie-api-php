<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/release-authorization
 */
class ReleasePaymentAuthorizationRequest extends Request
{
    protected static string $method = Method::POST;

    public function __construct(
        private string $paymentId,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return 'payments/'.$this->paymentId.'/release-authorization';
    }
}
