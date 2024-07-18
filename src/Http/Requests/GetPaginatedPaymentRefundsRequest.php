<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;

class GetPaginatedPaymentRefundsRequest extends Request
{
    use IsPaginatedRequest;

    /**
     * Define the HTTP method.
     */
    protected string $method = MollieApiClient::HTTP_GET;

    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\RefundCollection::class;

    protected string $paymentId;

    public function __construct(
        string $paymentId,
        array $filters = []
    ) {
        $this->paymentId = $paymentId;
        $this->filters = $filters;
    }

    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}/refunds";
    }
}
