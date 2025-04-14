<?php

namespace Tests\Http\Auth;

use Mollie\Api\Http\Auth\BearerTokenAuthenticator;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Repositories\ArrayStore;
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

        $headers = $this->createMock(ArrayStore::class);
        $headers
            ->expects($this->once())
            ->method('add')
            ->with('Authorization', "Bearer {$token}");

        $pendingRequest = $this->createMock(PendingRequest::class);
        $pendingRequest
            ->expects($this->once())
            ->method('headers')
            ->willReturn($headers);

        $authenticator->authenticate($pendingRequest);
    }

    /**
     * @test
     */
    public function authenticate_with_token_trimming()
    {
        $token = '  test_token_with_spaces  ';
        $trimmedToken = 'test_token_with_spaces';
        $authenticator = new BearerTokenAuthenticator($token);

        $headers = $this->createMock(ArrayStore::class);
        $headers
            ->expects($this->once())
            ->method('add')
            ->with('Authorization', "Bearer {$trimmedToken}");

        $pendingRequest = $this->createMock(PendingRequest::class);
        $pendingRequest
            ->expects($this->once())
            ->method('headers')
            ->willReturn($headers);

        $authenticator->authenticate($pendingRequest);
    }
}
