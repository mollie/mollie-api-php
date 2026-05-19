<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Payout;
use Mollie\Api\Types\Method;

class CancelPayoutRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Payout::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payouts/{$this->id}";
    }
}
