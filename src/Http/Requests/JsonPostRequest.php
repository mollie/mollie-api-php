<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasBody;
use Mollie\Api\MollieApiClient;

abstract class JsonPostRequest extends Request implements HasBody
{
    /**
     * Define the HTTP method.
     */
    protected string $method = MollieApiClient::HTTP_POST;

    public array $body = [];

    public function __construct(array $data)
    {
        $this->body = $data;
    }

    public function getBody(): string
    {
        return json_encode($this->body);
    }
}
