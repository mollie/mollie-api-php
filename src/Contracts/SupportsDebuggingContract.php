<?php

namespace Mollie\Api\Contracts;

interface SupportsDebuggingContract
{
    public function debugRequest(?callable $debugger = null, bool $die = false): self;

    public function debugResponse(?callable $debugger = null, bool $die = false): self;

    public function debug(bool $die = false): self;
}
