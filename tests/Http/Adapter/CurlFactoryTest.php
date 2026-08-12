<?php

declare(strict_types=1);

namespace Tests\Http\Adapter;

use Composer\CaBundle\CaBundle;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Adapter\CurlFactory;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class CurlFactoryTest extends TestCase
{
    #[Test]
    public function configures_tls_verification_and_ca_bundle_options(): void
    {
        $factory = CurlFactory::new('https://api.mollie.com/v2/payments', $this->pendingRequest());

        $options = $this->optionsFor($factory);

        $this->assertSame(true, $options[CURLOPT_SSL_VERIFYPEER]);
        $this->assertSame(2, $options[CURLOPT_SSL_VERIFYHOST]);
        $this->assertSame(CaBundle::getBundledCaBundlePath(), $options[CURLOPT_CAINFO]);
        $this->assertFileExists($options[CURLOPT_CAINFO]);
    }

    /**
     * @return array<int, mixed>
     */
    private function optionsFor(CurlFactory $factory): array
    {
        $property = new ReflectionProperty($factory, 'options');
        $property->setAccessible(true);

        return $property->getValue($factory);
    }

    private function pendingRequest(): PendingRequest
    {
        return new PendingRequest(new MockMollieClient, new DynamicGetRequest('/v2/payments'));
    }
}
