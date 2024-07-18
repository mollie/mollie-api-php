<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;

class GetPaymentRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected string $method = MollieApiClient::HTTP_GET;

    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass = \Mollie\Api\Resources\Payment::class;

    public string $paymentId;

    public array $filters;

    public function __construct(
        string $paymentId,
        array $filters = []
    ) {
        // add guard method

        $this->paymentId = $paymentId;
        $this->filters = $filters;
    }

    /**
     * Resolve the resource path.
     *
     * @return string
     */
    public function resolveResourcePath(): string
    {
        $id = urlencode($this->paymentId);

        return "payments/{$id}";
    }

    public function getQuery(): array
    {
        return $this->filters;
    }
}
