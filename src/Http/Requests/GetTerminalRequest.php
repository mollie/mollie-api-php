<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Types\Method;

class GetTerminalRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Terminal::class;

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
        return "terminals/{$this->id}";
    }
}
