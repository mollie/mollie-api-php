<?php

namespace Mollie\Api;

use Mollie\Api\Helpers\Arr;
use ReflectionClass;
use ReflectionProperty;

class Helpers
{
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param  object|class-string  $class
     * @return array<class-string, class-string>
     */
    public static function classUsesRecursive($class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += static::traitUsesRecursive($class);
        }

        return array_unique($results);
    }

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  class-string  $trait
     * @return array<class-string, class-string>
     */
    public static function traitUsesRecursive(string $trait): array
    {
        /** @var array<class-string, class-string> $traits */
        $traits = class_uses($trait) ?: [];

        foreach ($traits as $trait) {
            $traits += static::traitUsesRecursive($trait);
        }

        return $traits;
    }

    /**
     * Get the properties of a class.
     *
     * @param  string|class-string  $class
     * @param  int  $flag
     * @return ReflectionProperty[]
     */
    public static function getProperties($class, $flag = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE): array
    {
        $reflection = new ReflectionClass($class);

        return $reflection->getProperties($flag);
    }

    /**
     * Filter out the properties that are not part of the given class.
     *
     * @param  string|class-string  $class
     */
    public static function filterByProperties($class, array $array): array
    {
        $properties = array_map(
            fn (ReflectionProperty $prop) => $prop->getName(),
            static::getProperties($class)
        );

        return array_filter(
            $array,
            fn ($key) => ! in_array($key, $properties, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Compose a value to a new form if it is truthy.
     *
     * @param  mixed  $value
     * @param  string|callable  $composable
     * @param  mixed  $default
     * @return mixed
     */
    public static function compose($value, $composable, $default = null)
    {
        // If the value is an instance of the composable class, return it.
        if (is_string($composable) && $value instanceof $composable) {
            return $value;
        }

        $composable = is_string($composable)
            ? fn ($value) => new $composable($value)
            : $composable;

        return (bool) $value ? $composable($value) : $default;
    }

    /**
     * Extract a boolean value if not already a boolean.
     *
     * @param  mixed  $value
     */
    public static function extractBool($value, string $key, bool $default = false): bool
    {
        return is_bool($value)
            ? $value
            : Arr::get($value, $key, $default);
    }
}
