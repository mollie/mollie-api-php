<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RouteCollection;
use Mollie\Api\Types\Method;

class ListPaymentRoutesRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = RouteCollection::class;

    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes";
    }
}
