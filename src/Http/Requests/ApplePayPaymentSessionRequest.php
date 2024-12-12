<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\RequestApplePayPaymentSessionPayload;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class ApplePayPaymentSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    public static string $targetResourceClass = AnyResource::class;

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
