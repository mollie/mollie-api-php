<?php

namespace Mollie\Api\Utils;

class Str
{
    /**
     * Convert a string to lowercase (UTF-8 safe).
     */
    public static function lower(string $value): string
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

    public static function snake(string $value, string $delimiter = '_'): string
    {
        // Normalize hyphens to spaces so kebab-case becomes words
        $value = str_replace('-', ' ', $value);
        // Convert to StudlyCase words, removing spaces
        $value = preg_replace('/\s+/u', '', ucwords($value));

        // Insert delimiter before capitals and lowercase the result
        return static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
    }

    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, (string) $search, true);

        return $result === false ? $subject : $result;
    }
}
