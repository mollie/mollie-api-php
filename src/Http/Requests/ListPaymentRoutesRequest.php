<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\RouteCollection;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\RouteCollection>
 */
class ListPaymentRoutesRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = RouteCollection::class;

    public function __construct(
        private string $paymentId,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes";
    }
}
