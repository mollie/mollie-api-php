<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class AnyQuery implements Arrayable
{
    use ComposableFromArray;

    private ?array $data = [];

    public function __construct(?array $data = [])
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data ?? [];
    }
}
