<?php

declare(strict_types=1);

namespace Tests\Http\Auth;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Auth\BearerTokenAuthenticator;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BearerTokenAuthenticatorTest extends TestCase
{
    #[Test]
    public function authenticate()
    {
        $token = 'test_token';
        $authenticator = new BearerTokenAuthenticator($token);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest('payments'));

        $authenticator->authenticate($pendingRequest);

        $this->assertSame("Bearer {$token}", $pendingRequest->headers()->get('Authorization'));
    }

    #[Test]
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
