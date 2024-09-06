<?php

namespace Mollie\Api\Traits;

trait HandlesTestmode
{
    protected bool $testmode = false;

    public function enableTestmode(): static
    {
        $this->testmode = true;

        return $this;
    }

    public function disableTestmode(): static
    {
        $this->testmode = false;

        return $this;
    }
}
