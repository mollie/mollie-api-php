<?php

declare(strict_types=1);

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

        return rtrim($baseUrl, '/').'/'.static::encodeRelativePath(ltrim($endpoint, '/'));
    }

    /**
     * Check if the URL is a valid URL
     */
    public static function isValid(string $url): bool
    {
        return ! empty(filter_var($url, FILTER_VALIDATE_URL));
    }

    public static function encodeRelativePath(string $path): string
    {
        [$path, $suffix] = static::splitPathSuffix($path);

        return implode('/', array_map(
            fn (string $segment): string => static::encodePathSegment($segment),
            explode('/', $path)
        )).$suffix;
    }

    private static function encodePathSegment(string $segment): string
    {
        if ($segment === '.') {
            return '%2E';
        }

        if ($segment === '..') {
            return '%2E%2E';
        }

        return rawurlencode($segment);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitPathSuffix(string $path): array
    {
        $queryPosition = strpos($path, '?');
        $fragmentPosition = strpos($path, '#');
        $positions = array_filter(
            [$queryPosition, $fragmentPosition],
            static fn ($position): bool => $position !== false
        );

        if ($positions === []) {
            return [$path, ''];
        }

        $suffixPosition = min($positions);

        return [substr($path, 0, $suffixPosition), substr($path, $suffixPosition)];
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
