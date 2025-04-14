<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Types\Method;

class DynamicGetRequest extends DynamicRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    public function __construct(
        string $url,
        array $query = []
    ) {
        parent::__construct($url);

        $this->query()->merge($query);
    }
}
