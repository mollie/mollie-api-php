<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Helpers\Arr;
use stdClass;

class AnyResource extends BaseResource
{
    public array $attributes = [];

    public function __get(string $name): mixed
    {
        return Arr::get($this->attributes, $name);
    }

    public function fill(array|stdClass $attributes): void
    {
        $this->attributes = $attributes instanceof stdClass ? (array) $attributes : $attributes;
    }
}
