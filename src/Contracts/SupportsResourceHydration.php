<?php

namespace Mollie\Api\Contracts;

interface SupportsResourceHydration
{
    public static function hydrate(bool $shouldAutoHydrate = true): void;

    public function shouldAutoHydrate(): bool;

    public function getTargetResourceClass(): string;
}
