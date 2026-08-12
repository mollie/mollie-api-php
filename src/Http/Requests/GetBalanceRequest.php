<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/balances-api/get-balance
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Balance>
 */
class GetBalanceRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Balance::class;

    public function __construct(
        private string $id,
    ) {
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "balances/{$this->id}";
    }
}
