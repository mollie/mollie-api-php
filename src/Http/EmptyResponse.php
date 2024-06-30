<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;

class EmptyResponse implements ResponseContract
{
    public function status(): int
    {
        throw new \RuntimeException('No response status code available');
    }

    public function body(): string
    {
        return '';
    }

    public function decode(): \stdClass
    {
        return (object)[];
    }

    public function isEmpty(): bool
    {
        return true;
    }
}
