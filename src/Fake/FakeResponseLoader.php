<?php

namespace Mollie\Api\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Utils\Arr;

class FakeResponseLoader
{
    public static function load(string $key): string
    {
        $path = Arr::join([
            __DIR__,
            'Responses',
            $key.'.json',
        ], DIRECTORY_SEPARATOR);

        $contents = file_get_contents($path);

        if (! $contents) {
            throw new LogicException("Response file {$path} not found");
        }

        return $contents;
    }
}
