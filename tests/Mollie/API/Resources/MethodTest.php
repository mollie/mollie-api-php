<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    public function testIssuersNullWorks()
    {
        $method = new Method($this->createMock(MollieApiClient::class));
        $this->assertNull($method->issuers);

        $issuers = $method->issuers();

        $this->assertInstanceOf(IssuerCollection::class, $issuers);
        $this->assertCount(0, $issuers);
    }
}
