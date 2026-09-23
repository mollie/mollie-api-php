<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Types\Method;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DynamicRequestTest extends TestCase
{
    #[Test]
    public function it_accepts_valid_resource_class()
    {
        $request = new class('some-url') extends DynamicRequest {
            protected static string $method = Method::GET;
        };

        $request->setHydratableResource(Payment::class);

        $this->assertEquals(Payment::class, $request->getHydratableResource());
        $this->assertSame(Payment::class, $request->getHydratableResourceTarget());
    }

    #[Test]
    public function it_accepts_valid_collection_class()
    {
        $request = new class('some-url') extends DynamicRequest {
            protected static string $method = Method::GET;
        };

        $request->setHydratableResource(PaymentCollection::class);

        $this->assertSame(PaymentCollection::class, $request->getHydratableResourceTarget());
    }

    #[Test]
    public function it_resolves_correct_resource_path()
    {
        $url = 'https://example.org';
        $request = new class($url) extends DynamicRequest {
            protected static string $method = Method::GET;
        };

        $request->setHydratableResource(Payment::class);

        $this->assertEquals($url, $request->resolveResourcePath());
    }
}
