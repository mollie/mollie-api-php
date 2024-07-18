<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasBody;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;

class RefundPaymentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected string $method = MollieApiClient::HTTP_POST;

    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\Refund::class;

    public string $paymentId;

    public function __construct(
        string $paymentId,
        array $data
    ) {
        $this->paymentId = $paymentId;
        $this->body = $data;
    }

    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}/refunds";
    }
}
