<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/terminals-api/get-terminal
 */
class GetTerminalRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Terminal::class;

    public function __construct(
        private string $id,
    )
    {
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "terminals/{$this->id}";
    }
}
