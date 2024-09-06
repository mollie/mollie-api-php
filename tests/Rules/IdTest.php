<?php

namespace Tests\Rules;

use Mollie\Api\Http\Request;
use Mollie\Api\Rules\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    /** @test */
    public function starts_with_prefix_method_returns_instance()
    {
        $prefix = 'ord_';
        $rule = Id::startsWithPrefix($prefix);

        $this->assertInstanceOf(Id::class, $rule);
    }

    /** @test */
    public function validate_throws_exception_if_not_instance_of_request()
    {
        $rule = new Id('ord_');

        $nonRequestContext = new \stdClass; // Generic object to simulate incorrect context

        $rule->validate('ord_12345', $nonRequestContext, function ($message) {
            $this->assertEquals('The Id rule can only be used on a Request instance.', $message);
        });
    }

    /** @test */
    public function validate_throws_exception_if_id_does_not_start_with_prefix()
    {
        $rule = new Id('ord_');
        $request = new TestRequest;

        $rule->validate('inv_12345', $request, function ($message) {
            $this->assertEquals("Invalid order ID: 'inv_12345'. A resource ID should start with 'ord_'.", $message);
        });
    }

    /** @test */
    public function validate_does_not_invoke_callback_if_id_starts_with_prefix()
    {
        $rule = new Id('ord_');
        $request = new TestRequest;

        $rule->validate('ord_12345', $request, function () {
            $this->fail('Callback should not be invoked for a valid ID.');
        });

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    /** @test */
    public function get_resource_type_returns_lowercase_class_basename()
    {
        $rule = new Id('ord_');
        $request = new TestRequest;

        $resourceType = $rule->getResourceType($request);

        $this->assertEquals('order', $resourceType);
    }
}

class TestRequest extends Request
{
    public static string $targetResourceClass = 'Order';

    public function resolveResourcePath(): string
    {
        return 'test';
    }
}
