<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Utils\Arr;
use stdClass;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class AnyResource extends BaseResource
{
    public array $attributes = [];

    /**
     * @return mixed
     */
    public function __get(string $name)
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
