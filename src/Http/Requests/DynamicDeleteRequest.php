<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Types\Method;

class DynamicDeleteRequest extends DynamicRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private ?bool $testmode = null;

    public function __construct(
        string $url,
        string $resourceClass = '',
        ?bool $testmode = null
    ) {
        parent::__construct($url, $resourceClass);

        $this->testmode = $testmode;
    }

    protected function defaultQuery(): array
    {
        return [
            'testmode' => $this->testmode,
        ];
    }
}
