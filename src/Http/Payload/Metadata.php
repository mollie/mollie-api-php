<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;

class Metadata implements DataProvider
{
    public array $metadata;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function data(): string
    {
        return @json_encode($this->metadata);
    }
}
