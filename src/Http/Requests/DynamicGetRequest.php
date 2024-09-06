<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;

class DynamicGetRequest extends DynamicRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;
}
