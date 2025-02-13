<?php

namespace Mollie\Api\Traits;

trait HandlesTestmode
{
    protected ?bool $testmode = null;

    public function test(bool $testmode = true): self
    {
        $this->testmode = $testmode;

        return $this;
    }

    public function getTestmode(): ?bool
    {
        return $this->testmode;
    }
}
