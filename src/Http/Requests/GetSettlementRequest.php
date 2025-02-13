<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\Method;

class GetSettlementRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected $hydratableResource = Settlement::class;

    private string $id;

    public function __construct(
        string $id
    ) {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return "settlements/{$this->id}";
    }
}
