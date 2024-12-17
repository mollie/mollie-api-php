<?php

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    public function test_issuers_null_works()
    {
        $method = new Method(
            $this->createMock(MollieApiClient::class),
            $this->createMock(Response::class)
        );
        $this->assertNull($method->issuers);

        $issuers = $method->issuers();

        $this->assertInstanceOf(IssuerCollection::class, $issuers);
        $this->assertCount(0, $issuers);
    }
}
