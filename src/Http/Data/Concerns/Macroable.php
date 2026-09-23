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
 *
 * The macro map is held externally on {@see MacroRegistry} so this trait
 * can be used by readonly classes (which cannot declare non-readonly
 * static properties on their own).
 */
trait Macroable
{
    public static function macro(string $name, callable $macro): void
    {
        MacroRegistry::set(static::class, $name, $macro);
    }

    public static function hasMacro(string $name): bool
    {
        return MacroRegistry::has(static::class, $name);
    }

    public static function flushMacros(): void
    {
        MacroRegistry::flush(static::class);
    }

    /**
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (! MacroRegistry::has(static::class, $method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = MacroRegistry::get(static::class, $method);

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
        if (! MacroRegistry::has(static::class, $method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = MacroRegistry::get(static::class, $method);

        if ($macro instanceof Closure) {
            $macro = Closure::bind($macro, $this, static::class);
        }

        return $macro(...$parameters);
    }
}
