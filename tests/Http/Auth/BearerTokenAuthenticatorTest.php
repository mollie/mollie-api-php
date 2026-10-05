<?php

namespace Tests\Http\Auth;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Auth\BearerTokenAuthenticator;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use PHPUnit\Framework\TestCase;

class BearerTokenAuthenticatorTest extends TestCase
{
    /**
     * @test
     */
    public function authenticate()
    {
        $token = 'test_token';
        $authenticator = new BearerTokenAuthenticator($token);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest('payments'));

        $authenticator->authenticate($pendingRequest);

        $this->assertSame("Bearer {$token}", $pendingRequest->headers()->get('Authorization'));
    }

    /**
     * @test
     */
    public function authenticate_with_token_trimming()
    {
        $token = '  test_token_with_spaces  ';
        $trimmedToken = 'test_token_with_spaces';
        $authenticator = new BearerTokenAuthenticator($token);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest('payments'));

        $authenticator->authenticate($pendingRequest);

        $this->assertSame("Bearer {$trimmedToken}", $pendingRequest->headers()->get('Authorization'));
    }
}
