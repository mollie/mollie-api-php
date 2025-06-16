<?php

namespace Tests\Fixtures\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Requests\DynamicRequest;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DynamicDeleteRequest extends DynamicRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private array $payload = [];

    public function __construct(
        string $url,
        array $payload = []
    ) {
        parent::__construct($url);

        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload;
    }
}
