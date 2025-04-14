<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DynamicPutRequest extends DynamicRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::PUT;

    public function __construct(
        string $url,
        array $payload = [],
        array $query = []
    ) {
        parent::__construct($url);

        $this->payload()->merge($payload);
        $this->query()->merge($query);
    }
}
