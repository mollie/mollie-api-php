<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Utils\Arr;

class FakeResponseLoader
{
    public static function load(string $key, string $lookupFolder = 'Responses'): string
    {
        if (! in_array($lookupFolder, ['Responses', 'Events'])) {
            throw new LogicException("Lookup folder must be either 'Responses' or 'Events'");
        }

        $path = Arr::join([
            __DIR__,
            $lookupFolder,
            $key.'.json',
        ], DIRECTORY_SEPARATOR);

        try {
            $contents = file_get_contents($path);
        } catch (\Throwable $e) {
            throw new LogicException("{$lookupFolder} file {$path} not found");
        }

        return $contents;
    }

    public static function loadEventBlueprint(): string
    {
        return self::load('blueprint', 'Events');
    }
}
