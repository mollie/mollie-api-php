<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Terminal;

class GetTerminalRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Terminal::class;

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