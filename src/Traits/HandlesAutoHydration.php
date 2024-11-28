<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\Request;

trait HandlesAutoHydration
{
    protected static $hydrationSettingResolver = null;

    public static function shouldAutoHydrate(bool $shouldAutoHydrate = true): void
    {
        static::$hydrationSettingResolver = static function () use ($shouldAutoHydrate) {
            Request::hydrate($shouldAutoHydrate);
        };
    }

    /**
     * @return mixed
     */
    public function getHydrationResolver()
    {
        return static::$hydrationSettingResolver;
    }

    public function evaluateHydrationSetting(): void
    {
        (is_callable(static::$hydrationSettingResolver)
            ? static::$hydrationSettingResolver
            : static function () {
                return (bool) static::$hydrationSettingResolver;
            })();
    }
}