<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;

class NoResponse implements ResponseContract
{
    public function status(): int
    {
        throw new \RuntimeException('No response status code available');
    }

    public function body(): string
    {
        return '';
    }

    public function json(): \stdClass
    {
        return (object)[];
    }
}
