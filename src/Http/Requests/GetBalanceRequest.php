<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Types\Method;

class GetBalanceRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    protected $hydratableResource = Balance::class;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->id}";
    }
}
