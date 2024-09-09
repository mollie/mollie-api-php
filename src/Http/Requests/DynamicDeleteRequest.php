<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DynamicDeleteRequest extends DynamicRequest implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private ?bool $testmode = null;

    private array $body = [];

    public function __construct(
        string $url,
        string $resourceClass = '',
        ?bool $testmode = null,
        array $body = []
    ) {
        parent::__construct($url, $resourceClass);

        $this->testmode = $testmode;
        $this->body = $body;
    }

    protected function defaultQuery(): array
    {
        return [
            'testmode' => $this->testmode,
        ];
    }

    protected function defaultPayload(): array
    {
        return $this->body;
    }
}
