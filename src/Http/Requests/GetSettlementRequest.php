<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/settlements-api/get-settlement
 */
class GetSettlementRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Settlement::class;

    public function __construct(
        private string $id,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "settlements/{$this->id}";
    }
}
