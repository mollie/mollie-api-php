<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Query\GetPaymentCaptureQuery;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Types\Method;

class GetPaymentCaptureRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Capture::class;

    private string $paymentId;

    private string $captureId;

    private ?GetPaymentCaptureQuery $query;

    public function __construct(string $paymentId, string $captureId, ?GetPaymentCaptureQuery $query = null)
    {
        $this->paymentId = $paymentId;
        $this->captureId = $captureId;
        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query?->toArray() ?? [];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures/{$this->captureId}";
    }
}
