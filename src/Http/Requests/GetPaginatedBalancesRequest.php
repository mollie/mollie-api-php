<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\BalanceCollection;

class GetPaginatedBalancesRequest extends Request
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
    public static string $targetResourceClass = BalanceCollection::class;

    /**
     * Resolve the resource path.
     *
     * @return string
     */
    public function resolveResourcePath(): string
    {
        return "balances";
    }
}
