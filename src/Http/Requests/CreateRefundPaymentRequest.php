<?php

namespace Mollie\Api\Http\Requests;

class CreateRefundPaymentRequest extends JsonPostRequest
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\Refund::class;

    public string $paymentId;

    public function __construct(
        string $paymentId,
        array $data
    ) {
        parent::__construct(data: $data);

        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}/refunds";
    }
}
