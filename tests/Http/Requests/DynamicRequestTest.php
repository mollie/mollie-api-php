<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\Method;
use PHPUnit\Framework\TestCase;

class DynamicRequestTest extends TestCase
{
    /** @test */
    public function it_accepts_valid_resource_class()
    {
        $request = new class('some-url') extends DynamicRequest {
            protected static string $method = Method::GET;
        };

        $request->setHydratableResource(Payment::class);

        $this->assertEquals(Payment::class, $request->getHydratableResource());
    }

    /** @test */
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
