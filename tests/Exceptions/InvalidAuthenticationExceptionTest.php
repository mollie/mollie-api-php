<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use Mollie\Api\Exceptions\InvalidAuthenticationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InvalidAuthenticationExceptionTest extends TestCase
{
    #[Test]
    public function default_message_does_not_include_raw_token(): void
    {
        $token = 'invalid_token_that_must_not_be_logged';

        $exception = new InvalidAuthenticationException($token);

        self::assertSame($token, $exception->getToken());
        self::assertStringContainsString('Invalid authentication token', $exception->getMessage());
        self::assertStringNotContainsString($token, $exception->getMessage());
    }

    #[Test]
    public function custom_message_does_not_include_raw_token(): void
    {
        $token = 'custom_token_that_must_not_be_logged';

        $exception = new InvalidAuthenticationException($token, "Invalid token {$token}");

        self::assertSame($token, $exception->getToken());
        self::assertStringNotContainsString($token, $exception->getMessage());
    }
}
