<?php

namespace Mollie\Api\Http\Data;

use Stringable;

class Metadata implements Stringable
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return @json_encode($this->data);
    }
}
