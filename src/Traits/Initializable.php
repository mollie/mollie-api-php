<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Utils\Utility;
use ReflectionClass;

trait Initializable
{
    protected function initializeTraits(): void
    {
        foreach (Utility::classUsesRecursive(static::class) as $trait) {
            $trait = new ReflectionClass($trait);

            $method = 'initialize'.$trait->getShortName();

            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }
}
