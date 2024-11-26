<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\RequestApplePayPaymentSessionPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class ApplePayPaymentSessionRequest extends Request implements HasPayload, ResourceHydratable
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    private RequestApplePayPaymentSessionPayload $payload;

    public function __construct(RequestApplePayPaymentSessionPayload $payload)
    {
        $this->payload = $payload;
    }

    public function resolveResourcePath(): string
    {
        return 'wallets/applepay/sessions';
    }

    public function defaultPayload(): array
    {
        return $this->payload->toArray();
    }
}
