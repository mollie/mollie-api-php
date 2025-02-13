<?php

namespace Mollie\Api\Utils;

class Url
{
    /**
     * Join a base URL and a path, ensuring that there is only one slash between them.
     */
    public static function join(string $baseUrl, string $endpoint): string
    {
        if (static::isValid($endpoint)) {
            return $endpoint;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/');
    }

    /**
     * Check if the URL is a valid URL
     */
    public static function isValid(string $url): bool
    {
        return ! empty(filter_var($url, FILTER_VALIDATE_URL));
    }

    /**
     * Parses query string into an array
     *
     * @return array<string, mixed>
     */
    public static function parseQuery(string $query): array
    {
        if ($query === '') {
            return [];
        }

        $parameters = [];

        foreach (explode('&', $query) as $parameter) {
            $name = urldecode((string) strtok($parameter, '='));
            $value = urldecode((string) strtok('='));

            if (! $name || str_starts_with($parameter, '=')) {
                continue;
            }

            $parameters[$name] = $value;
        }

        return $parameters;
    }
}
