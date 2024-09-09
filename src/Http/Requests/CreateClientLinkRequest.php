<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Payload\CreateClientLinkPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\ClientLink;
use Mollie\Api\Types\Method;

class CreateClientLinkRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = ClientLink::class;

    private CreateClientLinkPayload $payload;

    public function __construct(CreateClientLinkPayload $payload)
    {
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->data();
    }

    public function resolveResourcePath(): string
    {

        return 'client-links';
    }
}
