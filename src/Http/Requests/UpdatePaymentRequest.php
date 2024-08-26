<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\MollieApiClient;

class UpdatePaymentRequest extends Request
{
    use HasJsonBody;

    protected string $method = MollieApiClient::HTTP_PATCH;

    public string $paymentId;

    public function __construct(string $paymentId, array $body)
    {
        $this->paymentId = $paymentId;
        $this->body = $body;
    }

    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}";
    }
}
