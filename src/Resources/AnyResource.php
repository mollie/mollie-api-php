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

    /**
     * @param  array|stdClass  $attributes
     */
    public function fill($attributes): void
    {
        $this->attributes = $attributes instanceof stdClass ? (array) $attributes : $attributes;
    }
}
