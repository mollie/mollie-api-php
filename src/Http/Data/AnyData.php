<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Traits\ComposableFromArray;

class AnyData implements Resolvable
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
