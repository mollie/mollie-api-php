<?php

declare(strict_types=1);

namespace Tests;

use Mollie\Api\Exceptions\MissingAuthenticationException;
use Mollie\Api\Http\Auth\AccessTokenAuthenticator;
use Mollie\Api\Http\Auth\ApiKeyAuthenticator;
use Mollie\Api\MollieApiClient;
use PHPUnit\Framework\TestCase;

class FromEnvTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $envSnapshot = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['MOLLIE_API_KEY', 'MOLLIE_ACCESS_TOKEN'] as $name) {
            $this->envSnapshot[$name] = $_ENV[$name] ?? false;
            unset($_ENV[$name], $_SERVER[$name]);
            putenv($name);
        }
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

    /** @test */
    public function uses_mollie_api_key_env_var(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM';

        $client = MollieApiClient::fromEnv();

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $client->getAuthenticator());
    }

    /** @test */
    public function falls_back_to_mollie_access_token_env_var(): void
    {
        $_ENV['MOLLIE_ACCESS_TOKEN'] = 'access_abcdefghijklmnopqrstuvwxyz';

        $client = MollieApiClient::fromEnv();

        $this->assertInstanceOf(AccessTokenAuthenticator::class, $client->getAuthenticator());
    }

    /** @test */
    public function prefers_api_key_over_access_token(): void
    {
        $_ENV['MOLLIE_API_KEY'] = 'test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM';
        $_ENV['MOLLIE_ACCESS_TOKEN'] = 'access_abcdefghijklmnopqrstuvwxyz';

        $client = MollieApiClient::fromEnv();

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $client->getAuthenticator());
    }

    /** @test */
    public function throws_when_no_env_var_is_set(): void
    {
        $this->expectException(MissingAuthenticationException::class);

        MollieApiClient::fromEnv();
    }
}
