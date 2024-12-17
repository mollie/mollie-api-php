<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\UpdateProfilePayload;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateProfileRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Profile::class;

    private string $id;

    private UpdateProfilePayload $payload;

    public function __construct(string $id, UpdateProfilePayload $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "profiles/{$this->id}";
    }
}
