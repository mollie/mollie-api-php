<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\PaymentCollection;

class GetPaginatedPaymentsRequest extends Request implements IsIteratable
{
    use IsPaginatedRequest;
    use IsIteratableRequest;

    /**
     * Define the HTTP method.
     */
    protected string $method = MollieApiClient::HTTP_GET;

    /**
     * The resource class the request should be casted to.
     *
     * @var string
     */
    public static string $targetResourceClass = PaymentCollection::class;

    /**
     * Resolve the resource path.
     *
     * @return string
     */
    public function resolveResourcePath(): string
    {
        return "payments";
    }
}
