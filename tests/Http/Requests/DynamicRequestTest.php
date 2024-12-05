<?php

namespace Tests\Http\Requests;

use InvalidArgumentException;
use Mollie\Api\Http\Requests\DynamicRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\Method;
use Tests\TestCase;

class DynamicRequestTest extends TestCase
{
    /** @test */
    public function it_throws_exception_for_invalid_resource_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The resource class 'NonExistentClass' does not exist.");

        /** @phpstan-ignore-next-line */
        new class('some-url', 'NonExistentClass') extends DynamicRequest
        {
            protected static string $method = Method::GET;
        };
    }

    /** @test */
    public function it_accepts_valid_resource_class()
    {
        $request = new class('some-url', Payment::class) extends DynamicRequest
        {
            protected static string $method = Method::GET;
        };

        $this->assertEquals(Payment::class, $request->getTargetResourceClass());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $url = 'https://example.org';
        $request = new class($url, Payment::class) extends DynamicRequest
        {
            protected static string $method = Method::GET;
        };

        $this->assertEquals($url, $request->resolveResourcePath());
    }
}
