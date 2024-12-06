<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;

class Metadata implements DataProvider
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): string
    {
        return @json_encode($this->data);
    }
}
