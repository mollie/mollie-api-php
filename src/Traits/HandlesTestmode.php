<?php

namespace Mollie\Api\Traits;

trait HandlesTestmode
{
    protected bool $testmode = false;

    public function enableTestmode(): self
    {
        $this->testmode = true;

        return $this;
    }

    public function disableTestmode(): self
    {
        $this->testmode = false;

        return $this;
    }
}
