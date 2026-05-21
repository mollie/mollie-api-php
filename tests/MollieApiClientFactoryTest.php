<?php

declare(strict_types=1);

namespace Tests;

use Mollie\Api\Exceptions\InvalidAuthenticationException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\MollieApiClient;
use PHPUnit\Framework\TestCase;

class MollieApiClientFactoryTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $envSnapshot = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->snapshotEnv('MOLLIE_API_KEY');
        $this->snapshotEnv('MOLLIE_ACCESS_TOKEN');
    }

    protected function tearDown(): void
    {
        foreach ($this->envSnapshot as $name => $value) {
            unset($_ENV[$name], $_SERVER[$name]);
            putenv($name);

            if ($value !== false) {
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }

        parent::tearDown();
    }

    public function test_sandbox_returns_client_with_test_key(): void
    {
        $client = MollieApiClient::sandbox('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $authenticator = $client->getAuthenticator();

        $this->assertSame('https://api.mollie.com/v2', $client->resolveBaseUrl());
        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
        $this->assertTrue($authenticator->isTestToken());
    }

    public function test_production_returns_client_with_live_key(): void
    {
        $client = MollieApiClient::production('live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

        $authenticator = $client->getAuthenticator();

        $this->assertSame('https://api.mollie.com/v2', $client->resolveBaseUrl());
        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
        $this->assertFalse($authenticator->isTestToken());
    }

    public function test_sandbox_throws_for_live_key(): void
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Expected key starting with 'test_'");

        MollieApiClient::sandbox('live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    public function test_production_throws_for_test_key(): void
    {
        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Expected key starting with 'live_'");

        MollieApiClient::production('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    public function test_from_env_with_test_key_uses_sandbox_factory(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        $client = MollieApiClient::fromEnv();
        $authenticator = $client->getAuthenticator();

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
        $this->assertTrue($authenticator->isTestToken());
    }

    public function test_from_env_with_live_key_uses_production_factory(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        $client = MollieApiClient::fromEnv();
        $authenticator = $client->getAuthenticator();

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
        $this->assertFalse($authenticator->isTestToken());
    }

    public function test_from_env_with_access_token_keeps_oauth_path(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'access_xxxxxxxxxxxxxxxxxxxxxxxxxxxx';

        $client = MollieApiClient::fromEnv();

        $this->assertInstanceOf(AccessTokenAuthenticator::class, $client->getAuthenticator());
    }

    public function test_from_env_with_unknown_prefix_throws(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'invalid_xxxxxxxxxxxxxxxxxxxxxxxxxx';

        $this->expectException(InvalidAuthenticationException::class);
        $this->expectExceptionMessage("Unknown Mollie token prefix. Expected key starting with 'test_', 'live_', or 'access_'");

        MollieApiClient::fromEnv();
    }

    private function snapshotEnv(string $name): void
    {
        $this->envSnapshot[$name] = $_ENV[$name] ?? false;
        unset($_ENV[$name], $_SERVER[$name]);
        putenv($name);
    }
}
