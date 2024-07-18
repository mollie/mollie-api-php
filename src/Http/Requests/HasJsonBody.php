<?php

namespace Mollie\Api\Http\Requests;

trait HasJsonBody
{
    public array $body = [];

    public function getBody(): string
    {
        return json_encode($this->body);
    }
}
