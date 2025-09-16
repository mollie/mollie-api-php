<?php

namespace Mollie\Api\Utils;

class Str
{
    /**
     * Convert a string to lowercase (UTF-8 safe).
     */
    public static function lower($value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert a string to kebab-case.
     */
    public static function kebab(string $name): string
    {
        $name = str_replace('_', '-', $name);
        $name = preg_replace('/(?<!^)\p{Lu}/u', '-$0', $name);
        $name = self::lower($name);
        $name = preg_replace('/-+/', '-', $name);

        return trim($name, '-');
    }
}
