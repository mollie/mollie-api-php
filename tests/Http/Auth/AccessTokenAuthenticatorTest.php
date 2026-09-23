<?php

declare(strict_types=1);

namespace Tests\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AccessTokenAuthenticatorTest extends TestCase
{
    #[Test]
    public function accepts_valid_access_token()
    {
        $authenticator = new AccessTokenAuthenticator('access_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $this->assertInstanceOf(AccessTokenAuthenticator::class, $authenticator);
    }

    #[Test]
    public function throws_exception_for_invalid_token()
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Invalid OAuth access token. An access token must start with 'access_'.");

        new AccessTokenAuthenticator('invalid_token');
    }

    #[Test]
    public function throws_exception_for_api_key()
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Invalid OAuth access token. An access token must start with 'access_'.");

        new AccessTokenAuthenticator('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');
    }
}
