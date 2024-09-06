<?php

namespace Mollie\Api\Contracts;

interface Hydratable
{
    public static function shouldAutoHydrate(bool $shouldAutoHydrate = true): void;

    /**
     * @return mixed
     */
    public function getHydrationResolver();

    public function evaluateHydrationSetting(): void;
}
