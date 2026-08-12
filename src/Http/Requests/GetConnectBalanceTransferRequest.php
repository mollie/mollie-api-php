<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\ConnectBalanceTransfer>
 */
class GetConnectBalanceTransferRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = ConnectBalanceTransfer::class;

    public function __construct(
        private string $id,
    ) {
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "connect/balance-transfers/{$this->id}";
    }
}
