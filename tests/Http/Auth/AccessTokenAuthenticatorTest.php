<?php

namespace Tests\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use PHPUnit\Framework\TestCase;

class AccessTokenAuthenticatorTest extends TestCase
{
    /**
     * @test
     */
    public function accepts_valid_access_token()
    {
        new AccessTokenAuthenticator('access_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        // no exception was thrown
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_token()
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Invalid OAuth access token. An access token must start with 'access_'.");

        new AccessTokenAuthenticator('invalid_token');
    }

    /**
     * @test
     */
    public function throws_exception_for_api_key()
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Invalid OAuth access token. An access token must start with 'access_'.");

        new AccessTokenAuthenticator('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');
    }
}
