<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\Session;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected $hydratableResource = Session::class;

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
