<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;

class DynamicGetRequest extends DynamicRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    private array $query = [];

    public function __construct(
        string $url,
        array $query = []
    ) {
        parent::__construct($url);

        $this->query = $query;
    }

    protected function defaultQuery(): array
    {
        return $this->query;
    }
}
