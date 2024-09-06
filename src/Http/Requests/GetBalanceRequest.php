<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Types\Method;

class GetBalanceRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Balance::class;

    private string $id;

    private bool $testmode;

    public function __construct(string $id, bool $testmode = false)
    {
        $this->id = $id;
        $this->testmode = $testmode;
    }

    protected function defaultQuery(): array
    {
        return [
            'testmode' => $this->testmode,
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->id}";
    }
}
