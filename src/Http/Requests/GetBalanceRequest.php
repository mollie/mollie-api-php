<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Balance;
use Mollie\Api\Types\Method;

class GetBalanceRequest extends SimpleRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Balance::class;

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->id}";
    }
}
