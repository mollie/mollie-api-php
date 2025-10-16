<?php

namespace Mollie\Api\Http\Auth;

final class TokenValidator
{
    private const API_KEY_PATTERN = '/^(live|test)_\w{30,}$/';
    private const ACCESS_TOKEN_PATTERN = '/^access_\w+$/';

    public static function isApiKey(string $token): bool
    {
        return preg_match(self::API_KEY_PATTERN, \trim($token)) === 1;
    }

    public static function isAccessToken(string $token): bool
    {
        return preg_match(self::ACCESS_TOKEN_PATTERN, \trim($token)) === 1;
    }
}
