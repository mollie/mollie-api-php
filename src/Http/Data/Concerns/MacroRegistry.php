<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data\Concerns;

/**
 * Class-keyed registry of macro callables.
 *
 * Lives outside the Macroable trait so the trait can be used by readonly
 * classes — a readonly class cannot declare a non-readonly static
 * property, which rules out keeping the map on the trait itself.
 */
final class MacroRegistry
{
    /**
     * @var array<class-string, array<string, callable>>
     */
    private static array $macros = [];

    public static function set(string $class, string $name, callable $macro): void
    {
        self::$macros[$class][$name] = $macro;
    }

    public static function has(string $class, string $name): bool
    {
        return isset(self::$macros[$class][$name]);
    }

    public static function get(string $class, string $name): callable
    {
        return self::$macros[$class][$name];
    }

    public static function flush(string $class): void
    {
        unset(self::$macros[$class]);
    }
}
