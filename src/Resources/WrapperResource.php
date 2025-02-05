<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;

class WrapperResource
{
    private string $wrapper;

    public function __construct(string $wrapper)
    {
        if (! is_subclass_of($wrapper, IsWrapper::class)) {
            throw new \InvalidArgumentException("The wrapper class '{$wrapper}' does not implement the IsWrapper interface.");
        }

        $this->wrapper = $wrapper;
    }

    public function getWrapper(): string
    {
        return $this->wrapper;
    }
}
