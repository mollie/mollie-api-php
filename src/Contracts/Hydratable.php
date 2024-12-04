<?php

namespace Mollie\Api\Contracts;

interface Hydratable
{
    public static function setAutoHydrate(bool $shouldAutoHydrate = true): void;

    /**
     * @return mixed
     */
    public function getHydrationResolver();

    public function evaluateHydrationSetting(): void;
}
