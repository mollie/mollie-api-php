<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Traits\ComposableFromArray;

class AnyQuery extends Query
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
