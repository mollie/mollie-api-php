<?php

namespace Mollie\Api\Contracts;

interface SupportsResourceHydration
{
    public function getTargetResourceClass(): string;
}
