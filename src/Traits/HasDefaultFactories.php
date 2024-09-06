<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Helpers\Factories;
use Nyholm\Psr7\Factory\Psr17Factory;

trait HasDefaultFactories
{
    private static ?Factories $factories = null;

    public function factories(): Factories
    {
        if (static::$factories) {
            return static::$factories;
        }

        $httpFactory = new Psr17Factory;

        return static::$factories = new Factories(
            requestFactory: $httpFactory,
            responseFactory: $httpFactory,
            streamFactory: $httpFactory,
            uriFactory: $httpFactory,
        );
    }
}
