<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    #[Test]
    public function issuers_null_works()
    {
        $method = new Method(new MockMollieClient);
        $method->setResponse($this->createMock(Response::class));
        $this->assertNull($method->issuers);

        $issuers = $method->issuers();

        $this->assertInstanceOf(IssuerCollection::class, $issuers);
        $this->assertCount(0, $issuers);
    }
}
