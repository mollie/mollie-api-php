<?php

namespace Mollie\Api\Utils;

use ArgumentCountError;

class Arr
{
    /**
     * Get all values for the given keys or return the default value.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(array $array, string $keys, $default = null)
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
     * Get and remove an item from an array using "dot" notation.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function pull(array &$array, string $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Remove the given keys from the array.
     */
    public static function except(array $array, $keys): array
    {
        foreach ((array) $keys as $key) {
            static::forget($array, $key);
        }

        return $array;
    }

    /**
     * Remove an item from an array using "dot" notation.
     */
    public static function forget(array &$array, string $key): void
    {
        $keys = explode('.', $key);
        $last = array_pop($keys);
        $array = &$array;

        foreach ($keys as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                break;
            }
            $array = &$array[$segment];
        }

        unset($array[$last]);
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
    public static function join(array $array, string $glue = ','): string
    {
        return implode($glue, array_filter($array));
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

    /**
     * Map the given array using the given callback.
     */
    public static function map(array $array, callable $callback)
    {
        $keys = array_keys($array);

        try {
            $items = array_map($callback, $array, $keys);
        } catch (ArgumentCountError $_) {
            $items = array_map($callback, $array);
        }

        return array_combine($keys, $items);
    }

    /**
     * Check if a value exists in an array of includes.
     *
     * @param  string|array<string>  $key
     * @param  mixed  $value
     *
     * @example includes(['includes' => ['payment']], 'includes', 'payment') => true
     * @example includes(['includes' => ['refund']], 'includes', 'payment') => false
     * @example includes(['includes' => ['payment' => 'foo']], 'includes.payment', 'foo') => true
     */
    public static function includes(array $array, $key, $value): bool
    {
        $keys = (array) $key;

        foreach ($keys as $k) {
            if (Arr::has($array, $k) && in_array($value, Arr::wrap(Arr::get($array, $k, [])))) {
                return true;
            }
        }

        return false;
    }
}
