<?php

namespace Tests\Http\Auth;

use Mollie\Api\Http\Auth\TokenValidator;
use PHPUnit\Framework\TestCase;

class TokenValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function determines_if_token_is_api_key()
    {
        $this->assertTrue(TokenValidator::isApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM'));
        $this->assertTrue(TokenValidator::isApiKey('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM'));
        $this->assertFalse(TokenValidator::isApiKey('invalid_api_key'));
    }

    /**
     * @test
     */
    public function determines_if_token_is_access_token()
    {
        $this->assertTrue(TokenValidator::isAccessToken('access_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM'));
        $this->assertFalse(TokenValidator::isAccessToken('invalid_access_token'));
    }
}
