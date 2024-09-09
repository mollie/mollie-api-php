<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Helpers;
use ReflectionClass;

trait Initializable
{
    protected function initializeTraits(): void
    {
        foreach (Helpers::classUsesRecursive(static::class) as $trait) {
            $trait = new ReflectionClass($trait);

            $method = 'initialize'.$trait->getShortName();

            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }
}
