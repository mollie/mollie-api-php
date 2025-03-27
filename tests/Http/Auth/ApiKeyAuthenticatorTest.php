<?php

namespace Tests\Http\Auth;

use Mollie\Api\Exceptions\InvalidAuthenticationException;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use PHPUnit\Framework\TestCase;

class ApiKeyAuthenticatorTest extends TestCase
{
    /**
     * @test
     */
    public function determines_if_token_is_test_token()
    {
        $token = 'test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM';
        $authenticator = new ApiKeyAuthenticator($token);

        $this->assertTrue($authenticator->isTestToken());
    }

    /**
     * @test
     */
    public function determines_if_token_is_live_token()
    {
        $token = 'live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM';
        $authenticator = new ApiKeyAuthenticator($token);

        $this->assertFalse($authenticator->isTestToken());
    }

    /**
     * @test
     */
    public function throws_exception_for_invalid_key()
    {
        $this->expectException(InvalidAuthenticationException::class);

        new ApiKeyAuthenticator('invalid_key');
    }

    /**
     * @test
     */
    public function throws_exception_for_short_key()
    {
        $this->expectException(InvalidAuthenticationException::class);

        new ApiKeyAuthenticator('test_tooshort');
    }
}
