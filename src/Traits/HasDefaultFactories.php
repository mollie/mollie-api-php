<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Utils\Factories;
use Nyholm\Psr7\Factory\Psr17Factory;

trait HasDefaultFactories
{
    private static ?Factories $factories = null;

    public function factories(): Factories
    {
        if (self::$factories) {
            return self::$factories;
        }

        $httpFactory = new Psr17Factory;

        return self::$factories = new Factories(
            $httpFactory,
            $httpFactory,
            $httpFactory,
            $httpFactory,
        );
    }
}
