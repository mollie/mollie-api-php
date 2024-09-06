<?php

namespace Mollie\Api\Helpers;

class Arr
{
    /**
     * Get all values for the given keys or return the default value.
     */
    public static function get(array $array, string $keys, mixed $default = null): mixed
    {
        $keys = explode('.', $keys);
        $value = $array;

        foreach ($keys as $key) {
            if (! is_array($value) || ! array_key_exists($key, $value)) {
                return $default;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Checks if the given key/s exist in the provided array.
     *
     * @param  array|string  $keys
     */
    public static function has(array $array, $keys): bool
    {
        $keys = (array) $keys;

        if (empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            $subSegment = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (! is_array($subSegment) || ! static::exists($subSegment, $segment)) {
                    return false;
                }

                $subSegment = $subSegment[$segment];
            }
        }

        return true;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  mixed  $key
     */
    public static function exists(array $array, $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Join array elements with a string.
     */
    public static function join(array $array, string $glue = ', '): string
    {
        return implode($glue, $array);
    }

    /**
     * Wrap the given value in an array if it is not already an array.
     *
     * @param  mixed  $array
     */
    public static function wrap($array): array
    {
        return is_array($array) ? $array : [$array];
    }
}
