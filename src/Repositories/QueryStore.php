<?php

namespace Mollie\Api\Repositories;

use Mollie\Api\Contracts\Repository;
use Mollie\Api\Utils\Arr;

class QueryStore extends ArrayStore implements Repository
{
    /**
     * Resolve the repository with special handling for query parameters
     *
     * Transforms boolean values to "true"/"false" strings
     */
    public function resolve(): self
    {
        $this->store = Arr::resolve($this->store, function ($value) {
            return $this->transformBooleans($value);
        });

        return $this;
    }

    /**
     * Transform boolean values in an array to "true"/"false" strings
     *
     * @param  mixed  $data
     * @return mixed
     */
    protected function transformBooleans($data)
    {
        if (is_array($data)) {
            return Arr::map($data, function ($value) {
                return $this->transformBooleans($value);
            });
        }

        if ($data === true) {
            return 'true';
        }

        if ($data === false) {
            return 'false';
        }

        return $data;
    }
}
