<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Utils\Arr;

class GetPaymentCaptureRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Capture::class;

    private string $paymentId;

    private string $captureId;

    private bool $embedPayment;

    public function __construct(string $paymentId, string $captureId, bool $embedPayment = false)
    {
        $this->paymentId = $paymentId;
        $this->captureId = $captureId;
        $this->embedPayment = $embedPayment;
    }

    protected function defaultQuery(): array
    {
        return [
            'embed' => Arr::join($this->embedPayment ? [PaymentIncludesQuery::PAYMENT] : []),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures/{$this->captureId}";
    }
}
