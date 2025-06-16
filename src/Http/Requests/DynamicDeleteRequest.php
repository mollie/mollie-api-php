<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DynamicDeleteRequest extends DynamicRequest implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    public function __construct(
        string $url,
        array $query = []
    ) {
        parent::__construct($url);

        $this->query()->merge($query);
    }
}
