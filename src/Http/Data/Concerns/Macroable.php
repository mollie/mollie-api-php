<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data\Concerns;

use BadMethodCallException;
use Closure;

/**
 * Lightweight macroable trait for Data objects.
 *
 * Allows registering static or instance macros at runtime. Closures are
 * bound to the invoking class/instance so macros may access protected
 * state. Intended for use on readonly Data value objects — macros must
 * never mutate state; return new instances instead.
 */
trait Macroable
{
    /**
     * @var array<string, callable>
     */
    protected static array $macros = [];

    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    public static function flushMacros(): void
    {
        static::$macros = [];
    }

    /**
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = Closure::bind($macro, null, static::class);
        }

        return $macro(...$parameters);
    }

    /**
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = Closure::bind($macro, $this, static::class);
        }

        return $macro(...$parameters);
    }
}
