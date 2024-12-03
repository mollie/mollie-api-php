<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Traits\ComposableFromArray;

class AnyPayload extends DataBag
{
    use ComposableFromArray;

    private ?array $data = [];

    public function __construct(?array $data = [])
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data ?? [];
    }
}
