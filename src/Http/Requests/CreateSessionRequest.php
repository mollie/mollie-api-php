<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\AnyData;
use Mollie\Api\Resources\Session;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    public static string $targetResourceClass = Session::class;

    private AnyData $payload;

    private AnyData $query;

    public function __construct(AnyData $payload, AnyData $query)
    {
        $this->payload = $payload;
        $this->query = $query;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    protected function defaultQuery(): array
    {
        return $this->query->toArray();
    }

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
